"""
AWS Cloud Resource Manager
=========================

Comprehensive AWS resource management and automation tool.
Demonstrates cloud provisioning, resource management, and AWS SDK usage.
"""

import boto3
import json
import time
import logging
from typing import Dict, List, Optional, Tuple
from datetime import datetime, timedelta
from dataclasses import dataclass, asdict
import threading
import sys

try:
    import botocore
    BOTO3_AVAILABLE = True
except ImportError:
    print("Warning: boto3 not available. AWS functionality will be limited.")
    BOTO3_AVAILABLE = False

@dataclass
class AWSResource:
    """AWS resource data structure"""
    resource_type: str
    resource_id: str
    resource_name: str
    region: str
    state: str
    tags: Dict[str, str]
    metadata: Dict

@dataclass
class CostAnalysis:
    """Cost analysis data structure"""
    service: str
    cost_amount: float
    currency: str
    period: str
    usage_type: str

class AWSManager:
    """AWS resource management and automation"""
    
    def __init__(self, region: str = 'us-west-2', access_key: str = None, 
                 secret_key: str = None, session_token: str = None):
        self.region = region
        self.resources = []
        self.cost_data = []
        
        if not BOTO3_AVAILABLE:
            print("boto3 is required for AWS operations")
            return
        
        # Setup AWS session
        if access_key and secret_key:
            session = boto3.Session(
                aws_access_key_id=access_key,
                aws_secret_access_key=secret_key,
                aws_session_token=session_token,
                region_name=region
            )
        else:
            session = boto3.Session(region_name=region)
        
        self.session = session
        self.clients = {}
        
        # Setup logging
        logging.basicConfig(level=logging.INFO)
        self.logger = logging.getLogger(__name__)
        
        # Initialize clients
        self._initialize_clients()
    
    def _initialize_clients(self):
        """Initialize AWS service clients"""
        if not BOTO3_AVAILABLE:
            return
        
        try:
            self.clients['ec2'] = self.session.client('ec2')
            self.clients['s3'] = self.session.client('s3')
            self.clients['rds'] = self.session.client('rds')
            self.clients['lambda'] = self.session.client('lambda')
            self.clients['iam'] = self.session.client('iam')
            self.clients['cloudwatch'] = self.session.client('cloudwatch')
            self.clients['ce'] = self.session.client('ce')  # Cost Explorer
            self.clients['autoscaling'] = self.session.client('autoscaling')
            self.clients['elb'] = self.session.client('elb')
            self.clients['ecs'] = self.session.client('ecs')
            self.clients['eks'] = self.session.client('eks')
            
            self.logger.info("AWS clients initialized successfully")
            
        except Exception as e:
            self.logger.error(f"Error initializing AWS clients: {e}")
    
    def test_connection(self) -> bool:
        """Test AWS connection"""
        if not BOTO3_AVAILABLE:
            return False
        
        try:
            # Test by calling STS GetCallerIdentity
            sts_client = self.session.client('sts')
            response = sts_client.get_caller_identity()
            
            self.logger.info(f"AWS connection successful. Account: {response.get('Account')}")
            return True
            
        except Exception as e:
            self.logger.error(f"AWS connection failed: {e}")
            return False
    
    def list_ec2_instances(self) -> List[AWSResource]:
        """List all EC2 instances"""
        if not BOTO3_AVAILABLE:
            return []
        
        instances = []
        
        try:
            response = self.clients['ec2'].describe_instances()
            
            for reservation in response['Reservations']:
                for instance in reservation['Instances']:
                    # Extract tags
                    tags = {}
                    for tag in instance.get('Tags', []):
                        tags[tag['Key']] = tag['Value']
                    
                    resource = AWSResource(
                        resource_type='EC2 Instance',
                        resource_id=instance['InstanceId'],
                        resource_name=tags.get('Name', instance['InstanceId']),
                        region=instance['Placement']['AvailabilityZone'][:-1],
                        state=instance['State']['Name'],
                        tags=tags,
                        metadata={
                            'instance_type': instance['InstanceType'],
                            'public_ip': instance.get('PublicIpAddress'),
                            'private_ip': instance.get('PrivateIpAddress'),
                            'launch_time': instance['LaunchTime'].isoformat(),
                            'security_groups': [sg['GroupName'] for sg in instance['SecurityGroups']],
                            'subnet_id': instance.get('SubnetId'),
                            'vpc_id': instance.get('VpcId')
                        }
                    )
                    
                    instances.append(resource)
            
            self.logger.info(f"Found {len(instances)} EC2 instances")
            
        except Exception as e:
            self.logger.error(f"Error listing EC2 instances: {e}")
        
        return instances
    
    def list_s3_buckets(self) -> List[AWSResource]:
        """List all S3 buckets"""
        if not BOTO3_AVAILABLE:
            return []
        
        buckets = []
        
        try:
            response = self.clients['s3'].list_buckets()
            
            for bucket in response['Buckets']:
                bucket_name = bucket['Name']
                
                # Get bucket location
                try:
                    location_response = self.clients['s3'].get_bucket_location(Bucket=bucket_name)
                    bucket_region = location_response['LocationConstraint'] or 'us-east-1'
                except:
                    bucket_region = 'unknown'
                
                # Get bucket tags
                tags = {}
                try:
                    tagging_response = self.clients['s3'].get_bucket_tagging(Bucket=bucket_name)
                    for tag in tagging_response['TagSet']:
                        tags[tag['Key']] = tag['Value']
                except:
                    pass
                
                # Get bucket size
                size_bytes = 0
                object_count = 0
                try:
                    # This is a simplified size calculation
                    paginator = self.clients['s3'].get_paginator('list_objects_v2')
                    for page in paginator.paginate(Bucket=bucket_name):
                        for obj in page.get('Contents', []):
                            size_bytes += obj['Size']
                            object_count += 1
                except:
                    pass
                
                resource = AWSResource(
                    resource_type='S3 Bucket',
                    resource_id=bucket_name,
                    resource_name=bucket_name,
                    region=bucket_region,
                    state='available',
                    tags=tags,
                    metadata={
                        'size_bytes': size_bytes,
                        'object_count': object_count,
                        'creation_date': bucket['CreationDate'].isoformat()
                    }
                )
                
                buckets.append(resource)
            
            self.logger.info(f"Found {len(buckets)} S3 buckets")
            
        except Exception as e:
            self.logger.error(f"Error listing S3 buckets: {e}")
        
        return buckets
    
    def list_rds_instances(self) -> List[AWSResource]:
        """List all RDS instances"""
        if not BOTO3_AVAILABLE:
            return []
        
        instances = []
        
        try:
            response = self.clients['rds'].describe_db_instances()
            
            for db_instance in response['DBInstances']:
                # Extract tags
                tags = {}
                try:
                    tags_response = self.clients['rds'].list_tags_for_resource(
                        ResourceName=db_instance['DBInstanceArn']
                    )
                    for tag in tags_response['TagList']:
                        tags[tag['Key']] = tag['Value']
                except:
                    pass
                
                resource = AWSResource(
                    resource_type='RDS Instance',
                    resource_id=db_instance['DBInstanceIdentifier'],
                    resource_name=db_instance['DBInstanceIdentifier'],
                    region=db_instance['AvailabilityZone'][:-1],
                    state=db_instance['DBInstanceStatus'],
                    tags=tags,
                    metadata={
                        'engine': db_instance['Engine'],
                        'engine_version': db_instance['EngineVersion'],
                        'instance_class': db_instance['DBInstanceClass'],
                        'allocated_storage': db_instance['AllocatedStorage'],
                        'storage_type': db_instance['StorageType'],
                        'multi_az': db_instance['MultiAZ'],
                        'publicly_accessible': db_instance['PubliclyAccessible'],
                        'endpoint': db_instance.get('Endpoint', {}).get('Address'),
                        'port': db_instance.get('Endpoint', {}).get('Port')
                    }
                )
                
                instances.append(resource)
            
            self.logger.info(f"Found {len(instances)} RDS instances")
            
        except Exception as e:
            self.logger.error(f"Error listing RDS instances: {e}")
        
        return instances
    
    def list_lambda_functions(self) -> List[AWSResource]:
        """List all Lambda functions"""
        if not BOTO3_AVAILABLE:
            return []
        
        functions = []
        
        try:
            response = self.clients['lambda'].list_functions()
            
            for function in response['Functions']:
                # Get function tags
                tags = {}
                try:
                    tags_response = self.clients['lambda'].list_tags(
                        Resource=function['FunctionArn']
                    )
                    tags = tags_response['Tags']
                except:
                    pass
                
                resource = AWSResource(
                    resource_type='Lambda Function',
                    resource_id=function['FunctionArn'],
                    resource_name=function['FunctionName'],
                    region=function['FunctionArn'].split(':')[3],
                    state=function['State'],
                    tags=tags,
                    metadata={
                        'runtime': function['Runtime'],
                        'handler': function['Handler'],
                        'code_size': function['CodeSize'],
                        'timeout': function['Timeout'],
                        'memory_size': function['MemorySize'],
                        'last_modified': function['LastModified'],
                        'description': function.get('Description', ''),
                        'layers': function.get('Layers', [])
                    }
                )
                
                functions.append(resource)
            
            self.logger.info(f"Found {len(functions)} Lambda functions")
            
        except Exception as e:
            self.logger.error(f"Error listing Lambda functions: {e}")
        
        return functions
    
    def create_ec2_instance(self, instance_type: str = 't2.micro', 
                           ami_id: str = 'ami-0c55b159cbfafe1f0',
                           key_name: str = None, security_group_ids: List[str] = None,
                           subnet_id: str = None, user_data: str = None,
                           tags: Dict[str, str] = None) -> Optional[str]:
        """Create a new EC2 instance"""
        if not BOTO3_AVAILABLE:
            return None
        
        try:
            instance_params = {
                'ImageId': ami_id,
                'InstanceType': instance_type,
                'MinCount': 1,
                'MaxCount': 1
            }
            
            if key_name:
                instance_params['KeyName'] = key_name
            
            if security_group_ids:
                instance_params['SecurityGroupIds'] = security_group_ids
            
            if subnet_id:
                instance_params['SubnetId'] = subnet_id
            
            if user_data:
                instance_params['UserData'] = user_data
            
            if tags:
                instance_params['TagSpecifications'] = [{
                    'ResourceType': 'instance',
                    'Tags': [{'Key': k, 'Value': v} for k, v in tags.items()]
                }]
            
            response = self.clients['ec2'].run_instances(**instance_params)
            instance_id = response['Instances'][0]['InstanceId']
            
            self.logger.info(f"EC2 instance created: {instance_id}")
            return instance_id
            
        except Exception as e:
            self.logger.error(f"Error creating EC2 instance: {e}")
            return None
    
    def create_s3_bucket(self, bucket_name: str, region: str = None,
                         tags: Dict[str, str] = None) -> bool:
        """Create a new S3 bucket"""
        if not BOTO3_AVAILABLE:
            return False
        
        try:
            create_params = {'Bucket': bucket_name}
            
            if region and region != 'us-east-1':
                create_params['CreateBucketConfiguration'] = {
                    'LocationConstraint': region
                }
            
            self.clients['s3'].create_bucket(**create_params)
            
            # Add tags if provided
            if tags:
                tag_set = [{'Key': k, 'Value': v} for k, v in tags.items()]
                self.clients['s3'].put_bucket_tagging(
                    Bucket=bucket_name,
                    Tagging={'TagSet': tag_set}
                )
            
            self.logger.info(f"S3 bucket created: {bucket_name}")
            return True
            
        except Exception as e:
            self.logger.error(f"Error creating S3 bucket: {e}")
            return False
    
    def get_cost_analysis(self, start_date: datetime = None, 
                         end_date: datetime = None) -> List[CostAnalysis]:
        """Get cost analysis from Cost Explorer"""
        if not BOTO3_AVAILABLE:
            return []
        
        cost_data = []
        
        try:
            if not start_date:
                start_date = datetime.now() - timedelta(days=30)
            
            if not end_date:
                end_date = datetime.now()
            
            response = self.clients['ce'].get_cost_and_usage(
                TimePeriod={
                    'Start': start_date.strftime('%Y-%m-%d'),
                    'End': end_date.strftime('%Y-%m-%d')
                },
                Granularity='MONTHLY',
                Metrics=['BlendedCost'],
                GroupBy=[
                    {'Type': 'DIMENSION', 'Key': 'SERVICE'},
                    {'Type': 'DIMENSION', 'Key': 'USAGE_TYPE'}
                ]
            )
            
            for result in response['ResultsByTime']:
                for group in result['Groups']:
                    service = group['Keys'][0]
                    usage_type = group['Keys'][1]
                    
                    for metric in group['Metrics']['BlendedCost']:
                        cost_analysis = CostAnalysis(
                            service=service,
                            cost_amount=float(metric['Amount']),
                            currency=metric['Unit'],
                            period=result['TimePeriod']['Start'],
                            usage_type=usage_type
                        )
                        cost_data.append(cost_analysis)
            
            self.logger.info(f"Retrieved cost analysis for {len(cost_data)} services")
            
        except Exception as e:
            self.logger.error(f"Error getting cost analysis: {e}")
        
        return cost_data
    
    def get_cloudwatch_metrics(self, namespace: str, metric_name: str,
                              dimensions: List[Dict[str, str]], 
                              start_time: datetime = None,
                              end_time: datetime = None) -> List[Dict]:
        """Get CloudWatch metrics"""
        if not BOTO3_AVAILABLE:
            return []
        
        metrics_data = []
        
        try:
            if not start_time:
                start_time = datetime.now() - timedelta(hours=1)
            
            if not end_time:
                end_time = datetime.now()
            
            response = self.clients['cloudwatch'].get_metric_statistics(
                Namespace=namespace,
                MetricName=metric_name,
                Dimensions=dimensions,
                StartTime=start_time,
                EndTime=end_time,
                Period=300,  # 5 minutes
                Statistics=['Average', 'Maximum', 'Minimum']
            )
            
            for datapoint in response['Datapoints']:
                metrics_data.append({
                    'timestamp': datapoint['Timestamp'],
                    'average': datapoint.get('Average'),
                    'maximum': datapoint.get('Maximum'),
                    'minimum': datapoint.get('Minimum'),
                    'unit': datapoint['Unit']
                })
            
            self.logger.info(f"Retrieved {len(metrics_data)} CloudWatch metrics")
            
        except Exception as e:
            self.logger.error(f"Error getting CloudWatch metrics: {e}")
        
        return metrics_data
    
    def start_instance(self, instance_id: str) -> bool:
        """Start an EC2 instance"""
        if not BOTO3_AVAILABLE:
            return False
        
        try:
            self.clients['ec2'].start_instances(InstanceIds=[instance_id])
            self.logger.info(f"Instance started: {instance_id}")
            return True
        except Exception as e:
            self.logger.error(f"Error starting instance {instance_id}: {e}")
            return False
    
    def stop_instance(self, instance_id: str) -> bool:
        """Stop an EC2 instance"""
        if not BOTO3_AVAILABLE:
            return False
        
        try:
            self.clients['ec2'].stop_instances(InstanceIds=[instance_id])
            self.logger.info(f"Instance stopped: {instance_id}")
            return True
        except Exception as e:
            self.logger.error(f"Error stopping instance {instance_id}: {e}")
            return False
    
    def terminate_instance(self, instance_id: str) -> bool:
        """Terminate an EC2 instance"""
        if not BOTO3_AVAILABLE:
            return False
        
        try:
            self.clients['ec2'].terminate_instances(InstanceIds=[instance_id])
            self.logger.info(f"Instance terminated: {instance_id}")
            return True
        except Exception as e:
            self.logger.error(f"Error terminating instance {instance_id}: {e}")
            return False
    
    def delete_s3_bucket(self, bucket_name: str, force: bool = False) -> bool:
        """Delete an S3 bucket"""
        if not BOTO3_AVAILABLE:
            return False
        
        try:
            if force:
                # Delete all objects first
                paginator = self.clients['s3'].get_paginator('list_objects_v2')
                for page in paginator.paginate(Bucket=bucket_name):
                    if 'Contents' in page:
                        objects = [{'Key': obj['Key']} for obj in page['Contents']]
                        self.clients['s3'].delete_objects(Bucket=bucket_name, Delete={'Objects': objects})
            
            self.clients['s3'].delete_bucket(Bucket=bucket_name)
            self.logger.info(f"S3 bucket deleted: {bucket_name}")
            return True
            
        except Exception as e:
            self.logger.error(f"Error deleting S3 bucket {bucket_name}: {e}")
            return False
    
    def generate_resource_report(self) -> str:
        """Generate comprehensive resource report"""
        if not BOTO3_AVAILABLE:
            return "boto3 is required for AWS operations"
        
        # Collect all resources
        ec2_instances = self.list_ec2_instances()
        s3_buckets = self.list_s3_buckets()
        rds_instances = self.list_rds_instances()
        lambda_functions = self.list_lambda_functions()
        
        report = []
        report.append("=" * 60)
        report.append("AWS RESOURCE REPORT")
        report.append("=" * 60)
        report.append(f"Region: {self.region}")
        report.append(f"Generated: {datetime.now().strftime('%Y-%m-%d %H:%M:%S')}")
        report.append("")
        
        # Summary
        report.append("RESOURCE SUMMARY:")
        report.append("-" * 40)
        report.append(f"EC2 Instances: {len(ec2_instances)}")
        report.append(f"S3 Buckets: {len(s3_buckets)}")
        report.append(f"RDS Instances: {len(rds_instances)}")
        report.append(f"Lambda Functions: {len(lambda_functions)}")
        report.append("")
        
        # EC2 Instances
        if ec2_instances:
            report.append("EC2 INSTANCES:")
            report.append("-" * 40)
            
            for instance in ec2_instances:
                report.append(f"ID: {instance.resource_id}")
                report.append(f"Name: {instance.resource_name}")
                report.append(f"Type: {instance.metadata['instance_type']}")
                report.append(f"State: {instance.state}")
                report.append(f"Public IP: {instance.metadata['public_ip']}")
                report.append(f"Private IP: {instance.metadata['private_ip']}")
                report.append(f"Launch Time: {instance.metadata['launch_time']}")
                report.append("")
        
        # S3 Buckets
        if s3_buckets:
            report.append("S3 BUCKETS:")
            report.append("-" * 40)
            
            for bucket in s3_buckets:
                size_gb = bucket.metadata['size_bytes'] / (1024**3)
                report.append(f"Name: {bucket.resource_id}")
                report.append(f"Region: {bucket.region}")
                report.append(f"Size: {size_gb:.2f} GB")
                report.append(f"Objects: {bucket.metadata['object_count']}")
                report.append("")
        
        # RDS Instances
        if rds_instances:
            report.append("RDS INSTANCES:")
            report.append("-" * 40)
            
            for instance in rds_instances:
                report.append(f"ID: {instance.resource_id}")
                report.append(f"Engine: {instance.metadata['engine']}")
                report.append(f"Version: {instance.metadata['engine_version']}")
                report.append(f"Class: {instance.metadata['instance_class']}")
                report.append(f"Storage: {instance.metadata['allocated_storage']} GB")
                report.append(f"State: {instance.state}")
                report.append("")
        
        # Lambda Functions
        if lambda_functions:
            report.append("LAMBDA FUNCTIONS:")
            report.append("-" * 40)
            
            for function in lambda_functions:
                report.append(f"Name: {function.resource_name}")
                report.append(f"Runtime: {function.metadata['runtime']}")
                report.append(f"Handler: {function.metadata['handler']}")
                report.append(f"Memory: {function.metadata['memory_size']} MB")
                report.append(f"Timeout: {function.metadata['timeout']}s")
                report.append(f"State: {function.state}")
                report.append("")
        
        return "\n".join(report)
    
    def save_resource_report(self, filename: str):
        """Save resource report to file"""
        report = self.generate_resource_report()
        
        with open(filename, 'w') as f:
            f.write(report)
        
        self.logger.info(f"Resource report saved: {filename}")

def create_sample_web_server_user_data() -> str:
    """Create user data for web server setup"""
    return '''#!/bin/bash
yum update -y
yum install -y httpd php
systemctl start httpd
systemctl enable httpd

# Create a simple PHP page
cat > /var/www/html/index.php << 'EOF'
<?php
echo "<h1>Hello from AWS!</h1>";
echo "<p>Server Time: " . date('Y-m-d H:i:s') . "</p>";
echo "<p>Instance ID: " . file_get_contents('http://169.254.169.254/latest/meta-data/instance-id') . "</p>";
?>
EOF

# Set proper permissions
chmod 644 /var/www/html/index.php
'''

def main():
    """Main function to demonstrate AWS manager"""
    print("=== AWS Cloud Resource Manager ===\n")
    
    if not BOTO3_AVAILABLE:
        print("boto3 is required for AWS operations.")
        print("Install with: pip install boto3")
        print("Then configure AWS credentials:")
        print("  aws configure")
        print("  OR set environment variables:")
        print("  AWS_ACCESS_KEY_ID")
        print("  AWS_SECRET_ACCESS_KEY")
        print("  AWS_DEFAULT_REGION")
        return
    
    # Initialize AWS manager
    manager = AWSManager()
    
    # Test connection
    print("1. Testing AWS connection...")
    if manager.test_connection():
        print("✅ AWS connection successful!")
    else:
        print("❌ AWS connection failed!")
        print("Please check your AWS credentials and permissions.")
        return
    
    # List resources
    print("\n2. Discovering AWS resources...")
    
    print("  EC2 Instances:")
    ec2_instances = manager.list_ec2_instances()
    for instance in ec2_instances[:5]:  # Show first 5
        print(f"    {instance.resource_id} - {instance.state} ({instance.metadata['instance_type']})")
    
    print(f"  Found {len(ec2_instances)} EC2 instances")
    
    print("  S3 Buckets:")
    s3_buckets = manager.list_s3_buckets()
    for bucket in s3_buckets[:5]:  # Show first 5
        size_gb = bucket.metadata['size_bytes'] / (1024**3)
        print(f"    {bucket.resource_id} - {size_gb:.2f} GB")
    
    print(f"  Found {len(s3_buckets)} S3 buckets")
    
    print("  RDS Instances:")
    rds_instances = manager.list_rds_instances()
    for instance in rds_instances[:3]:  # Show first 3
        print(f"    {instance.resource_id} - {instance.metadata['engine']} ({instance.state})")
    
    print(f"  Found {len(rds_instances)} RDS instances")
    
    print("  Lambda Functions:")
    lambda_functions = manager.list_lambda_functions()
    for function in lambda_functions[:3]:  # Show first 3
        print(f"    {function.resource_name} - {function.metadata['runtime']} ({function.state})")
    
    print(f"  Found {len(lambda_functions)} Lambda functions")
    
    # Cost analysis
    print("\n3. Analyzing costs (last 30 days)...")
    try:
        cost_data = manager.get_cost_analysis()
        
        if cost_data:
            # Group by service
            service_costs = {}
            for cost in cost_data:
                service = cost.service
                if service not in service_costs:
                    service_costs[service] = 0
                service_costs[service] += cost.cost_amount
            
            print("  Top 5 Services by Cost:")
            sorted_services = sorted(service_costs.items(), key=lambda x: x[1], reverse=True)
            for service, cost in sorted_services[:5]:
                print(f"    {service}: ${cost:.2f}")
            
            total_cost = sum(service_costs.values())
            print(f"  Total Cost: ${total_cost:.2f}")
        else:
            print("  No cost data available (Cost Explorer may not be enabled)")
    
    except Exception as e:
        print(f"  Error analyzing costs: {e}")
    
    # CloudWatch metrics
    print("\n4. Getting CloudWatch metrics...")
    try:
        # Get CPU utilization for EC2 instances
        if ec2_instances:
            instance_id = ec2_instances[0].resource_id
            metrics = manager.get_cloudwatch_metrics(
                namespace='AWS/EC2',
                metric_name='CPUUtilization',
                dimensions=[{'Name': 'InstanceId', 'Value': instance_id}]
            )
            
            if metrics:
                latest_metric = metrics[-1]
                print(f"  CPU Utilization for {instance_id}:")
                print(f"    Average: {latest_metric.get('average', 'N/A')}%")
                print(f"    Maximum: {latest_metric.get('maximum', 'N/A')}%")
                print(f"    Minimum: {latest_metric.get('minimum', 'N/A')}%")
    
    except Exception as e:
        print(f"  Error getting metrics: {e}")
    
    # Generate report
    print("\n5. Generating resource report...")
    manager.save_resource_report('aws_resource_report.txt')
    print("  Report saved: aws_resource_report.txt")
    
    # Interactive menu
    print("\n=== AWS Manager Interactive ===")
    
    while True:
        print("\nOptions:")
        print("1. List EC2 instances")
        print("2. List S3 buckets")
        print("3. List RDS instances")
        print("4. List Lambda functions")
        print("5. Create EC2 instance")
        print("6. Create S3 bucket")
        print("7. Start/stop/terminate instance")
        print("8. Get CloudWatch metrics")
        print("9. Generate report")
        print("0. Exit")
        
        choice = input("\nSelect option: ").strip()
        
        if choice == "0":
            break
        
        elif choice == "1":
            instances = manager.list_ec2_instances()
            print(f"\nEC2 Instances ({len(instances)}):")
            for instance in instances:
                print(f"  {instance.resource_id} - {instance.state} - {instance.metadata['instance_type']}")
        
        elif choice == "2":
            buckets = manager.list_s3_buckets()
            print(f"\nS3 Buckets ({len(buckets)}):")
            for bucket in buckets:
                size_gb = bucket.metadata['size_bytes'] / (1024**3)
                print(f"  {bucket.resource_id} - {size_gb:.2f} GB - {bucket.region}")
        
        elif choice == "3":
            instances = manager.list_rds_instances()
            print(f"\nRDS Instances ({len(instances)}):")
            for instance in instances:
                print(f"  {instance.resource_id} - {instance.metadata['engine']} - {instance.state}")
        
        elif choice == "4":
            functions = manager.list_lambda_functions()
            print(f"\nLambda Functions ({len(functions)}):")
            for function in functions:
                print(f"  {function.resource_name} - {function.metadata['runtime']} - {function.state}")
        
        elif choice == "5":
            print("\nCreating EC2 instance...")
            ami_id = input("Enter AMI ID (default: ami-0c55b159cbfafe1f0): ").strip() or "ami-0c55b159cbfafe1f0"
            instance_type = input("Enter instance type (default: t2.micro): ").strip() or "t2.micro"
            
            instance_id = manager.create_ec2_instance(
                instance_type=instance_type,
                ami_id=ami_id,
                user_data=create_sample_web_server_user_data(),
                tags={'Name': 'Demo Web Server', 'Environment': 'Demo'}
            )
            
            if instance_id:
                print(f"Instance created: {instance_id}")
                print("Waiting for instance to be ready...")
                time.sleep(30)
            else:
                print("Failed to create instance")
        
        elif choice == "6":
            bucket_name = input("Enter bucket name: ").strip()
            if bucket_name:
                success = manager.create_s3_bucket(
                    bucket_name=bucket_name,
                    tags={'Environment': 'Demo', 'Purpose': 'Testing'}
                )
                
                if success:
                    print(f"Bucket created: {bucket_name}")
                else:
                    print("Failed to create bucket")
        
        elif choice == "7":
            instance_id = input("Enter instance ID: ").strip()
            action = input("Enter action (start/stop/terminate): ").strip().lower()
            
            if action == "start":
                success = manager.start_instance(instance_id)
                print(f"Instance start: {'Success' if success else 'Failed'}")
            elif action == "stop":
                success = manager.stop_instance(instance_id)
                print(f"Instance stop: {'Success' if success else 'Failed'}")
            elif action == "terminate":
                success = manager.terminate_instance(instance_id)
                print(f"Instance terminate: {'Success' if success else 'Failed'}")
            else:
                print("Invalid action")
        
        elif choice == "8":
            service = input("Enter service (EC2/RDS/Lambda): ").strip().upper()
            metric = input("Enter metric name: ").strip()
            
            if service == "EC2":
                instances = manager.list_ec2_instances()
                if instances:
                    instance_id = input(f"Enter instance ID (default: {instances[0].resource_id}): ").strip() or instances[0].resource_id
                    
                    metrics = manager.get_cloudwatch_metrics(
                        namespace='AWS/EC2',
                        metric_name=metric,
                        dimensions=[{'Name': 'InstanceId', 'Value': instance_id}]
                    )
                    
                    if metrics:
                        print(f"\n{metric} for {instance_id}:")
                        for datapoint in metrics[-5:]:  # Show last 5
                            print(f"  {datapoint['timestamp']}: {datapoint.get('average', 'N/A')}")
                    else:
                        print("No metrics found")
                else:
                    print("No EC2 instances found")
            
            elif service == "RDS":
                instances = manager.list_rds_instances()
                if instances:
                    instance_id = input(f"Enter instance ID (default: {instances[0].resource_id}): ").strip() or instances[0].resource_id
                    
                    metrics = manager.get_cloudwatch_metrics(
                        namespace='AWS/RDS',
                        metric_name=metric,
                        dimensions=[{'Name': 'DBInstanceIdentifier', 'Value': instance_id}]
                    )
                    
                    if metrics:
                        print(f"\n{metric} for {instance_id}:")
                        for datapoint in metrics[-5:]:
                            print(f"  {datapoint['timestamp']}: {datapoint.get('average', 'N/A')}")
                    else:
                        print("No metrics found")
                else:
                    print("No RDS instances found")
            
            else:
                print("Unsupported service")
        
        elif choice == "9":
            filename = input("Enter filename (default: aws_report.txt): ").strip() or "aws_report.txt"
            manager.save_resource_report(filename)
            print(f"Report saved: {filename}")
        
        else:
            print("Invalid option")
    
    print("\n=== AWS Manager Demo Completed ===")
    print("Features demonstrated:")
    print("- AWS resource discovery (EC2, S3, RDS, Lambda)")
    print("- Resource creation and management")
    print("- Cost analysis and monitoring")
    print("- CloudWatch metrics")
    print("- Comprehensive reporting")
    print("- Interactive resource management")
    
    print("\nSupported AWS Services:")
    print("- EC2: Elastic Compute Cloud")
    print("- S3: Simple Storage Service")
    print("- RDS: Relational Database Service")
    print("- Lambda: Serverless Functions")
    print("- CloudWatch: Monitoring and Logging")
    print("- Cost Explorer: Cost Analysis")
    print("- IAM: Identity and Access Management")

if __name__ == "__main__":
    main()

"""
Example Usage:
1. Install boto3: pip install boto3
2. Configure AWS credentials: aws configure
3. Run manager: python aws_manager.py
4. Manage AWS resources interactively

Key Concepts:
- AWS SDK: Python interface to AWS services
- Resource Management: Create, list, update, delete resources
- Cost Analysis: Track and analyze AWS spending
- Monitoring: CloudWatch metrics and alerts
- Security: IAM roles and permissions
- Automation: Programmatic resource management

AWS Services:
- EC2: Virtual servers and compute
- S3: Object storage and files
- RDS: Managed databases
- Lambda: Serverless functions
- CloudWatch: Monitoring and logging
- Cost Explorer: Cost analysis
- IAM: Identity and access management

Operations:
- Resource Discovery: List and analyze resources
- Resource Creation: Provision new resources
- Resource Management: Start, stop, terminate
- Cost Analysis: Track spending patterns
- Monitoring: Collect metrics and logs
- Automation: Programmatically manage resources

Applications:
- Cloud resource management
- Cost optimization
- Infrastructure automation
- Monitoring and alerting
- Security auditing
- Capacity planning
- Multi-account management

Dependencies:
- boto3: pip install boto3
- AWS CLI: aws configure
- AWS Account: Required credentials

Best Practices:
- Use IAM roles instead of access keys
- Implement proper tagging strategy
- Monitor costs regularly
- Use security groups properly
- Enable logging and monitoring
- Regular security audits
- Backup critical resources
- Use multi-factor authentication

Security Notes:
- Never hardcode credentials
- Use least privilege principle
- Enable MFA on root account
- Regularly rotate credentials
- Monitor unusual activity
- Use VPC for network isolation
- Enable CloudTrail logging
"""
