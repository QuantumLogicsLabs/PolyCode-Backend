"""
Terraform Infrastructure Deployer
================================

Infrastructure as Code automation tool using Terraform.
Demonstrates cloud resource provisioning, configuration management, and deployment automation.
"""

import os
import json
import yaml
import subprocess
import time
import shutil
from typing import Dict, List, Optional, Tuple
from datetime import datetime
from dataclasses import dataclass, asdict
from enum import Enum
import hashlib
import logging

class DeploymentStatus(Enum):
    """Deployment status enumeration"""
    PENDING = "pending"
    PLANNING = "planning"
    APPLYING = "applying"
    SUCCESS = "success"
    FAILED = "failed"
    DESTROYED = "destroyed"

@dataclass
class TerraformResource:
    """Terraform resource definition"""
    type: str
    name: str
    config: Dict
    depends_on: List[str] = None
    variables: Dict[str, str] = None

@dataclass
class DeploymentResult:
    """Deployment execution result"""
    deployment_id: str
    status: DeploymentStatus
    start_time: datetime
    end_time: Optional[datetime]
    resources_created: List[str]
    resources_updated: List[str]
    resources_destroyed: List[str]
    outputs: Dict[str, str]
    logs: List[str]
    error_message: Optional[str] = None

class TerraformDeployer:
    """Terraform infrastructure deployment automation"""
    
    def __init__(self, workspace: str = "terraform_workspace"):
        self.workspace = workspace
        self.deployments = {}
        self.deployment_history = []
        self.current_deployment = None
        
        # Create workspace
        os.makedirs(workspace, exist_ok=True)
        
        # Setup logging
        logging.basicConfig(level=logging.INFO)
        self.logger = logging.getLogger(__name__)
        
        # Check Terraform installation
        self.terraform_available = self._check_terraform()
    
    def _check_terraform(self) -> bool:
        """Check if Terraform is installed"""
        try:
            result = subprocess.run(['terraform', 'version'], 
                                  capture_output=True, text=True, timeout=10)
            return result.returncode == 0
        except (subprocess.TimeoutExpired, FileNotFoundError):
            return False
    
    def create_terraform_project(self, name: str, provider: str, 
                                resources: List[TerraformResource],
                                variables: Dict[str, str] = None,
                                outputs: Dict[str, str] = None) -> str:
        """Create a Terraform project configuration"""
        project_id = hashlib.md5(f"{name}{time.time()}".encode()).hexdigest()[:8]
        
        project_dir = os.path.join(self.workspace, project_id)
        os.makedirs(project_dir, exist_ok=True)
        
        # Generate main.tf
        main_tf = self._generate_main_tf(provider, resources)
        
        # Generate variables.tf
        variables_tf = self._generate_variables_tf(variables or {})
        
        # Generate outputs.tf
        outputs_tf = self._generate_outputs_tf(outputs or {})
        
        # Save files
        with open(os.path.join(project_dir, 'main.tf'), 'w') as f:
            f.write(main_tf)
        
        with open(os.path.join(project_dir, 'variables.tf'), 'w') as f:
            f.write(variables_tf)
        
        with open(os.path.join(project_dir, 'outputs.tf'), 'w') as f:
            f.write(outputs_tf)
        
        # Generate terraform.tfvars
        if variables:
            tfvars_content = self._generate_tfvars(variables)
            with open(os.path.join(project_dir, 'terraform.tfvars'), 'w') as f:
                f.write(tfvars_content)
        
        project = {
            'id': project_id,
            'name': name,
            'provider': provider,
            'project_dir': project_dir,
            'resources': {f"{res.type}.{res.name}": asdict(res) for res in resources},
            'variables': variables or {},
            'outputs': outputs or {},
            'created_at': datetime.now().isoformat()
        }
        
        self.deployments[project_id] = project
        self.logger.info(f"Terraform project created: {name} ({project_id})")
        
        return project_id
    
    def _generate_main_tf(self, provider: str, resources: List[TerraformResource]) -> str:
        """Generate main.tf content"""
        main_tf = []
        
        # Provider configuration
        if provider == 'aws':
            main_tf.append('''
provider "aws" {
  region = var.aws_region
}
''')
        elif provider == 'azure':
            main_tf.append('''
provider "azurerm" {
  features {}
}
''')
        elif provider == 'gcp':
            main_tf.append('''
provider "google" {
  project = var.gcp_project
  region  = var.gcp_region
}
''')
        
        # Resources
        for resource in resources:
            resource_block = f'''
resource "{resource.type}" "{resource.name}" {{
'''
            
            # Add resource configuration
            for key, value in resource.config.items():
                if isinstance(value, str):
                    resource_block += f'  {key} = "{value}"\n'
                elif isinstance(value, bool):
                    resource_block += f'  {key} = {str(value).lower()}\n'
                elif isinstance(value, (int, float)):
                    resource_block += f'  {key} = {value}\n'
                elif isinstance(value, list):
                    resource_block += f'  {key} = {json.dumps(value)}\n'
                elif isinstance(value, dict):
                    resource_block += f'  {key} = {{\n'
                    for k, v in value.items():
                        if isinstance(v, str):
                            resource_block += f'    {k} = "{v}"\n'
                        else:
                            resource_block += f'    {k} = {v}\n'
                    resource_block += '  }\n'
            
            resource_block += '}\n'
            main_tf.append(resource_block)
        
        return '\n'.join(main_tf)
    
    def _generate_variables_tf(self, variables: Dict[str, str]) -> str:
        """Generate variables.tf content"""
        variables_tf = []
        
        for var_name, var_value in variables.items():
            var_type = 'string' if isinstance(var_value, str) else 'number'
            default_value = json.dumps(var_value) if isinstance(var_value, (list, dict)) else str(var_value)
            
            variables_tf.append(f'''
variable "{var_name}" {{
  description = "{var_name} variable"
  type        = {var_type}
  default     = {default_value}
}}
''')
        
        return '\n'.join(variables_tf)
    
    def _generate_outputs_tf(self, outputs: Dict[str, str]) -> str:
        """Generate outputs.tf content"""
        outputs_tf = []
        
        for output_name, output_value in outputs.items():
            outputs_tf.append(f'''
output "{output_name}" {{
  description = "{output_name} output"
  value       = {output_value}
}}
''')
        
        return '\n'.join(outputs_tf)
    
    def _generate_tfvars(self, variables: Dict[str, str]) -> str:
        """Generate terraform.tfvars content"""
        tfvars = []
        
        for var_name, var_value in variables.items():
            if isinstance(var_value, str):
                tfvars.append(f'{var_name} = "{var_value}"')
            else:
                tfvars.append(f'{var_name} = {var_value}')
        
        return '\n'.join(tfvars)
    
    def create_aws_web_app(self) -> str:
        """Create AWS web application infrastructure"""
        resources = [
            TerraformResource(
                type="aws_vpc",
                name="main",
                config={
                    "cidr_block": "10.0.0.0/16",
                    "enable_dns_hostnames": True,
                    "enable_dns_support": True,
                    "tags": {
                        "Name": "web-app-vpc",
                        "Environment": "production"
                    }
                }
            ),
            TerraformResource(
                type="aws_subnet",
                name="public",
                config={
                    "vpc_id": "${aws_vpc.main.id}",
                    "cidr_block": "10.0.1.0/24",
                    "availability_zone": "us-west-2a",
                    "map_public_ip_on_launch": True,
                    "tags": {
                        "Name": "web-app-public-subnet"
                    }
                },
                depends_on=["aws_vpc.main"]
            ),
            TerraformResource(
                type="aws_security_group",
                name="web",
                config={
                    "name": "web-app-sg",
                    "description": "Allow HTTP/HTTPS traffic",
                    "vpc_id": "${aws_vpc.main.id}",
                    "ingress": [
                        {
                            "from_port": 80,
                            "to_port": 80,
                            "protocol": "tcp",
                            "cidr_blocks": ["0.0.0.0/0"]
                        },
                        {
                            "from_port": 443,
                            "to_port": 443,
                            "protocol": "tcp",
                            "cidr_blocks": ["0.0.0.0/0"]
                        }
                    ],
                    "egress": [
                        {
                            "from_port": 0,
                            "to_port": 0,
                            "protocol": "-1",
                            "cidr_blocks": ["0.0.0.0/0"]
                        }
                    ]
                },
                depends_on=["aws_vpc.main"]
            ),
            TerraformResource(
                type="aws_instance",
                name="web",
                config={
                    "ami": "ami-0c55b159cbfafe1f0",  # Amazon Linux 2
                    "instance_type": "t2.micro",
                    "subnet_id": "${aws_subnet.public.id}",
                    "vpc_security_group_ids": ["${aws_security_group.web.id}"],
                    "user_data": '''#!/bin/bash
yum update -y
yum install -y httpd
systemctl start httpd
systemctl enable httpd
echo "<h1>Hello from Terraform!</h1>" > /var/www/html/index.html
''',
                    "tags": {
                        "Name": "web-app-instance",
                        "Environment": "production"
                    }
                },
                depends_on=["aws_subnet.public", "aws_security_group.web"]
            )
        ]
        
        variables = {
            "aws_region": "us-west-2"
        }
        
        outputs = {
            "instance_public_ip": "aws_instance.web.public_ip",
            "instance_id": "aws_instance.web.id",
            "vpc_id": "aws_vpc.main.id"
        }
        
        return self.create_terraform_project(
            name="AWS Web Application",
            provider="aws",
            resources=resources,
            variables=variables,
            outputs=outputs
        )
    
    def create_azure_web_app(self) -> str:
        """Create Azure web application infrastructure"""
        resources = [
            TerraformResource(
                type="azurerm_resource_group",
                name="main",
                config={
                    "name": "web-app-rg",
                    "location": "East US",
                    "tags": {
                        "Environment": "production",
                        "Project": "web-app"
                    }
                }
            ),
            TerraformResource(
                type="azurerm_app_service_plan",
                name="main",
                config={
                    "name": "web-app-plan",
                    "location": "${azurerm_resource_group.main.location}",
                    "resource_group_name": "${azurerm_resource_group.main.name}",
                    "sku": {
                        "tier": "Basic",
                        "size": "B1"
                    }
                },
                depends_on=["azurerm_resource_group.main"]
            ),
            TerraformResource(
                type="azurerm_app_service",
                name="main",
                config={
                    "name": "web-app-service",
                    "location": "${azurerm_resource_group.main.location}",
                    "resource_group_name": "${azurerm_resource_group.main.name}",
                    "app_service_plan_id": "${azurerm_app_service_plan.main.id}",
                    "site_config": {
                        "always_on": True,
                        "linux_fx_version": "PYTHON|3.9"
                    },
                    "app_settings": {
                        "WEBSITE_RUN_FROM_PACKAGE": "1"
                    }
                },
                depends_on=["azurerm_app_service_plan.main"]
            )
        ]
        
        variables = {}
        
        outputs = {
            "app_service_url": "azurerm_app_service.main.default_hostname",
            "resource_group_name": "azurerm_resource_group.main.name",
            "app_service_plan_id": "azurerm_app_service_plan.main.id"
        }
        
        return self.create_terraform_project(
            name="Azure Web Application",
            provider="azure",
            resources=resources,
            variables=variables,
            outputs=outputs
        )
    
    def create_gcp_web_app(self) -> str:
        """Create GCP web application infrastructure"""
        resources = [
            TerraformResource(
                type="google_compute_network",
                name="vpc_network",
                config={
                    "name": "web-app-network",
                    "auto_create_subnetworks": False
                }
            ),
            TerraformResource(
                type="google_compute_subnetwork",
                name="subnet",
                config={
                    "name": "web-app-subnet",
                    "ip_cidr_range": "10.0.0.0/24",
                    "region": "us-central1",
                    "network": "${google_compute_network.vpc_network.name}"
                },
                depends_on=["google_compute_network.vpc_network"]
            ),
            TerraformResource(
                type="google_compute_firewall",
                name="http",
                config={
                    "name": "allow-http",
                    "network": "${google_compute_network.vpc_network.name}",
                    "allow": [
                        {
                            "protocol": "tcp",
                            "ports": ["80"]
                        }
                    ],
                    "source_ranges": ["0.0.0.0/0"],
                    "target_tags": ["web-server"]
                },
                depends_on=["google_compute_network.vpc_network"]
            ),
            TerraformResource(
                type="google_compute_instance",
                name="web_server",
                config={
                    "name": "web-app-instance",
                    "machine_type": "e2-micro",
                    "zone": "us-central1-a",
                    "boot_disk": {
                        "initialize_params": {
                            "image": "debian-cloud/debian-10"
                        }
                    },
                    "network_interface": {
                        "network": "${google_compute_network.vpc_network.id}",
                        "subnetwork": "${google_compute_subnetwork.subnet.id}",
                        "access_config": {}  # External IP
                    },
                    "tags": ["web-server"],
                    "metadata_startup_script": '''#!/bin/bash
apt-get update
apt-get install -y apache2
systemctl start apache2
systemctl enable apache2
echo "<h1>Hello from Terraform on GCP!</h1>" > /var/www/html/index.html
'''
                },
                depends_on=["google_compute_subnetwork.subnet", "google_compute_firewall.http"]
            )
        ]
        
        variables = {
            "gcp_project": "my-project-id",
            "gcp_region": "us-central1"
        }
        
        outputs = {
            "instance_ip": "google_compute_instance.web_server.network_interface.0.access_config.0.nat_ip",
            "instance_id": "google_compute_instance.web_server.id",
            "network_name": "google_compute_network.vpc_network.name"
        }
        
        return self.create_terraform_project(
            name="GCP Web Application",
            provider="gcp",
            resources=resources,
            variables=variables,
            outputs=outputs
        )
    
    def deploy_infrastructure(self, project_id: str) -> DeploymentResult:
        """Deploy Terraform infrastructure"""
        if not self.terraform_available:
            raise RuntimeError("Terraform is not installed or not available")
        
        if project_id not in self.deployments:
            raise ValueError(f"Project {project_id} not found")
        
        project = self.deployments[project_id]
        self.current_deployment = project_id
        
        result = DeploymentResult(
            deployment_id=hashlib.md5(f"{project_id}{time.time()}".encode()).hexdigest()[:8],
            status=DeploymentStatus.PENDING,
            start_time=datetime.now(),
            end_time=None,
            resources_created=[],
            resources_updated=[],
            resources_destroyed=[],
            outputs={},
            logs=[]
        )
        
        self.logger.info(f"Deploying infrastructure: {project['name']}")
        
        try:
            # Change to project directory
            original_cwd = os.getcwd()
            os.chdir(project['project_dir'])
            
            # Initialize Terraform
            self._run_terraform_command(['init'], result.logs)
            
            # Plan deployment
            self._run_terraform_command(['plan', '-out=tfplan'], result.logs)
            
            # Apply deployment
            result.status = DeploymentStatus.APPLYING
            apply_result = self._run_terraform_command(['apply', '-auto-approve', 'tfplan'], result.logs)
            
            if apply_result['success']:
                result.status = DeploymentStatus.SUCCESS
                
                # Get outputs
                output_result = self._run_terraform_command(['output', '-json'], result.logs)
                if output_result['success']:
                    result.outputs = json.loads(output_result['stdout'])
                
                # Parse resources from logs
                result.resources_created = self._parse_resources_from_logs(result.logs, 'created')
                result.resources_updated = self._parse_resources_from_logs(result.logs, 'updated')
                
                self.logger.info(f"Deployment completed successfully: {project['name']}")
            else:
                result.status = DeploymentStatus.FAILED
                result.error_message = apply_result['stderr']
                self.logger.error(f"Deployment failed: {project['name']}")
        
        except Exception as e:
            result.status = DeploymentStatus.FAILED
            result.error_message = str(e)
            self.logger.error(f"Deployment error: {e}")
        
        finally:
            os.chdir(original_cwd)
            result.end_time = datetime.now()
            self.deployment_history.append(result)
            self.current_deployment = None
        
        return result
    
    def destroy_infrastructure(self, project_id: str) -> DeploymentResult:
        """Destroy Terraform infrastructure"""
        if not self.terraform_available:
            raise RuntimeError("Terraform is not installed or not available")
        
        if project_id not in self.deployments:
            raise ValueError(f"Project {project_id} not found")
        
        project = self.deployments[project_id]
        
        result = DeploymentResult(
            deployment_id=hashlib.md5(f"{project_id}{time.time()}".encode()).hexdigest()[:8],
            status=DeploymentStatus.PENDING,
            start_time=datetime.now(),
            end_time=None,
            resources_created=[],
            resources_updated=[],
            resources_destroyed=[],
            outputs={},
            logs=[]
        )
        
        self.logger.info(f"Destroying infrastructure: {project['name']}")
        
        try:
            # Change to project directory
            original_cwd = os.getcwd()
            os.chdir(project['project_dir'])
            
            # Initialize Terraform
            self._run_terraform_command(['init'], result.logs)
            
            # Destroy infrastructure
            result.status = DeploymentStatus.DESTROYED
            destroy_result = self._run_terraform_command(['destroy', '-auto-approve'], result.logs)
            
            if destroy_result['success']:
                result.resources_destroyed = self._parse_resources_from_logs(result.logs, 'destroyed')
                self.logger.info(f"Infrastructure destroyed: {project['name']}")
            else:
                result.status = DeploymentStatus.FAILED
                result.error_message = destroy_result['stderr']
                self.logger.error(f"Destroy failed: {project['name']}")
        
        except Exception as e:
            result.status = DeploymentStatus.FAILED
            result.error_message = str(e)
            self.logger.error(f"Destroy error: {e}")
        
        finally:
            os.chdir(original_cwd)
            result.end_time = datetime.now()
            self.deployment_history.append(result)
        
        return result
    
    def _run_terraform_command(self, command: List[str], logs: List[str]) -> Dict:
        """Run Terraform command and return result"""
        try:
            result = subprocess.run(
                ['terraform'] + command,
                capture_output=True,
                text=True,
                timeout=300  # 5 minutes timeout
            )
            
            logs.append(f"$ terraform {' '.join(command)}")
            logs.append(result.stdout)
            
            if result.stderr:
                logs.append(f"STDERR: {result.stderr}")
            
            return {
                'success': result.returncode == 0,
                'stdout': result.stdout,
                'stderr': result.stderr,
                'returncode': result.returncode
            }
        
        except subprocess.TimeoutExpired:
            error_msg = f"Command timed out: terraform {' '.join(command)}"
            logs.append(error_msg)
            return {
                'success': False,
                'stdout': '',
                'stderr': error_msg,
                'returncode': -1
            }
        
        except Exception as e:
            error_msg = f"Command failed: {e}"
            logs.append(error_msg)
            return {
                'success': False,
                'stdout': '',
                'stderr': error_msg,
                'returncode': -1
            }
    
    def _parse_resources_from_logs(self, logs: List[str], action: str) -> List[str]:
        """Parse resource names from Terraform logs"""
        resources = []
        
        for log_line in logs:
            if action.lower() in log_line.lower():
                # Look for resource patterns like "aws_instance.web: Creation complete"
                import re
                pattern = r'(\w+\.\w+):'
                matches = re.findall(pattern, log_line)
                resources.extend(matches)
        
        return list(set(resources))  # Remove duplicates
    
    def get_deployment_status(self, project_id: str) -> Optional[DeploymentResult]:
        """Get deployment status"""
        for result in reversed(self.deployment_history):
            if result.deployment_id.startswith(project_id):
                return result
        return None
    
    def generate_deployment_report(self, project_id: str) -> str:
        """Generate deployment report"""
        if project_id not in self.deployments:
            return f"Project {project_id} not found"
        
        project = self.deployments[project_id]
        deployment = self.get_deployment_status(project_id)
        
        report = []
        report.append("=" * 60)
        report.append("TERRAFORM DEPLOYMENT REPORT")
        report.append("=" * 60)
        report.append(f"Project: {project['name']} ({project_id})")
        report.append(f"Provider: {project['provider']}")
        report.append(f"Resources: {len(project['resources'])}")
        
        if deployment:
            report.append(f"Status: {deployment.status.value.upper()}")
            report.append(f"Start Time: {deployment.start_time}")
            report.append(f"End Time: {deployment.end_time}")
            
            if deployment.start_time and deployment.end_time:
                duration = deployment.end_time - deployment.start_time
                report.append(f"Duration: {duration}")
            
            if deployment.error_message:
                report.append(f"Error: {deployment.error_message}")
            
            report.append("")
            
            # Resources
            if deployment.resources_created:
                report.append("RESOURCES CREATED:")
                for resource in deployment.resources_created:
                    report.append(f"  - {resource}")
                report.append("")
            
            if deployment.resources_updated:
                report.append("RESOURCES UPDATED:")
                for resource in deployment.resources_updated:
                    report.append(f"  - {resource}")
                report.append("")
            
            if deployment.resources_destroyed:
                report.append("RESOURCES DESTROYED:")
                for resource in deployment.resources_destroyed:
                    report.append(f"  - {resource}")
                report.append("")
            
            # Outputs
            if deployment.outputs:
                report.append("OUTPUTS:")
                for key, value in deployment.outputs.items():
                    report.append(f"  {key}: {value}")
                report.append("")
            
            # Logs
            report.append("RECENT LOGS:")
            report.append("-" * 40)
            for log in deployment.logs[-10:]:  # Show last 10 log entries
                report.append(f"  {log}")
        
        else:
            report.append("No deployment history found")
        
        return "\n".join(report)
    
    def export_project_config(self, project_id: str, filename: str):
        """Export project configuration"""
        if project_id not in self.deployments:
            raise ValueError(f"Project {project_id} not found")
        
        project = self.deployments[project_id]
        
        with open(filename, 'w') as f:
            json.dump(project, f, indent=2)
        
        self.logger.info(f"Project configuration exported: {filename}")
    
    def import_project_config(self, filename: str) -> str:
        """Import project configuration"""
        with open(filename, 'r') as f:
            project = json.load(f)
        
        project_id = project['id']
        self.deployments[project_id] = project
        
        self.logger.info(f"Project configuration imported: {filename}")
        return project_id
    
    def cleanup_workspace(self):
        """Clean up workspace directory"""
        if os.path.exists(self.workspace):
            shutil.rmtree(self.workspace)
            os.makedirs(self.workspace, exist_ok=True)
            self.logger.info("Workspace cleaned")

def main():
    """Main function to demonstrate Terraform deployer"""
    print("=== Terraform Infrastructure Deployer ===\n")
    
    deployer = TerraformDeployer()
    
    if not deployer.terraform_available:
        print("Terraform is not installed or not available.")
        print("Please install Terraform and ensure it's in your PATH.")
        print("Download from: https://www.terraform.io/downloads.html")
        return
    
    print("Terraform is available!")
    
    # Create AWS web application
    print("\n1. Creating AWS Web Application Infrastructure...")
    try:
        aws_project_id = deployer.create_aws_web_app()
        print(f"AWS project created: {aws_project_id}")
    except Exception as e:
        print(f"Error creating AWS project: {e}")
    
    # Create Azure web application
    print("\n2. Creating Azure Web Application Infrastructure...")
    try:
        azure_project_id = deployer.create_azure_web_app()
        print(f"Azure project created: {azure_project_id}")
    except Exception as e:
        print(f"Error creating Azure project: {e}")
    
    # Create GCP web application
    print("\n3. Creating GCP Web Application Infrastructure...")
    try:
        gcp_project_id = deployer.create_gcp_web_app()
        print(f"GCP project created: {gcp_project_id}")
    except Exception as e:
        print(f"Error creating GCP project: {e}")
    
    # Show created projects
    print("\n4. Created Projects:")
    for project_id, project in deployer.deployments.items():
        print(f"  {project_id} - {project['name']} ({project['provider']})")
        print(f"    Resources: {len(project['resources'])}")
        print(f"    Directory: {project['project_dir']}")
    
    # Interactive menu
    print("\n=== Terraform Deployer Interactive ===")
    
    while True:
        print("\nOptions:")
        print("1. List projects")
        print("2. Deploy infrastructure")
        print("3. Destroy infrastructure")
        print("4. Show deployment status")
        print("5. Generate deployment report")
        print("6. Export project configuration")
        print("7. Import project configuration")
        print("8. Show Terraform files")
        print("9. Cleanup workspace")
        print("0. Exit")
        
        choice = input("\nSelect option: ").strip()
        
        if choice == "0":
            break
        
        elif choice == "1":
            print("\nAvailable Projects:")
            for project_id, project in deployer.deployments.items():
                deployment = deployer.get_deployment_status(project_id)
                status = deployment.status.value if deployment else "Not deployed"
                print(f"  {project_id} - {project['name']} ({project['provider']}) - {status}")
        
        elif choice == "2":
            project_id = input("Enter project ID: ").strip()
            if project_id in deployer.deployments:
                try:
                    print(f"Deploying project {project_id}...")
                    result = deployer.deploy_infrastructure(project_id)
                    print(f"Deployment completed: {result.status.value}")
                    
                    if result.outputs:
                        print("\nDeployment Outputs:")
                        for key, value in result.outputs.items():
                            print(f"  {key}: {value}")
                except Exception as e:
                    print(f"Deployment failed: {e}")
            else:
                print("Project not found")
        
        elif choice == "3":
            project_id = input("Enter project ID: ").strip()
            if project_id in deployer.deployments:
                try:
                    print(f"Destroying project {project_id}...")
                    result = deployer.destroy_infrastructure(project_id)
                    print(f"Destruction completed: {result.status.value}")
                except Exception as e:
                    print(f"Destruction failed: {e}")
            else:
                print("Project not found")
        
        elif choice == "4":
            project_id = input("Enter project ID: ").strip()
            if project_id in deployer.deployments:
                deployment = deployer.get_deployment_status(project_id)
                if deployment:
                    print(f"\nDeployment Status: {deployment.status.value}")
                    print(f"Start Time: {deployment.start_time}")
                    print(f"End Time: {deployment.end_time}")
                    
                    if deployment.resources_created:
                        print(f"Resources Created: {len(deployment.resources_created)}")
                    
                    if deployment.outputs:
                        print("Outputs:")
                        for key, value in deployment.outputs.items():
                            print(f"  {key}: {value}")
                else:
                    print("No deployment history found")
            else:
                print("Project not found")
        
        elif choice == "5":
            project_id = input("Enter project ID: ").strip()
            if project_id in deployer.deployments:
                report = deployer.generate_deployment_report(project_id)
                print("\n" + report)
            else:
                print("Project not found")
        
        elif choice == "6":
            project_id = input("Enter project ID: ").strip()
            filename = input("Enter filename: ").strip()
            
            if project_id in deployer.deployments:
                try:
                    deployer.export_project_config(project_id, filename)
                    print(f"Project configuration exported: {filename}")
                except Exception as e:
                    print(f"Export failed: {e}")
            else:
                print("Project not found")
        
        elif choice == "7":
            filename = input("Enter filename: ").strip()
            try:
                project_id = deployer.import_project_config(filename)
                print(f"Project configuration imported: {project_id}")
            except Exception as e:
                print(f"Import failed: {e}")
        
        elif choice == "8":
            project_id = input("Enter project ID: ").strip()
            if project_id in deployer.deployments:
                project = deployer.deployments[project_id]
                project_dir = project['project_dir']
                
                print(f"\nTerraform files in {project_dir}:")
                
                for filename in ['main.tf', 'variables.tf', 'outputs.tf', 'terraform.tfvars']:
                    file_path = os.path.join(project_dir, filename)
                    if os.path.exists(file_path):
                        print(f"\n{filename}:")
                        print("-" * 40)
                        with open(file_path, 'r') as f:
                            content = f.read()
                            print(content[:500] + "..." if len(content) > 500 else content)
            else:
                print("Project not found")
        
        elif choice == "9":
            deployer.cleanup_workspace()
            print("Workspace cleaned")
        
        else:
            print("Invalid option")
    
    print("\n=== Terraform Deployer Demo Completed ===")
    print("Features demonstrated:")
    print("- Terraform project creation")
    print("- Multi-cloud support (AWS, Azure, GCP)")
    print("- Infrastructure deployment")
    print("- Infrastructure destruction")
    print("- Deployment status tracking")
    print("- Configuration management")
    print("- Report generation")
    print("- Project import/export")
    
    print("\nSupported Cloud Providers:")
    print("- Amazon Web Services (AWS)")
    print("- Microsoft Azure")
    print("- Google Cloud Platform (GCP)")
    
    print("\nInfrastructure Components:")
    print("- Virtual Private Clouds (VPC)")
    print("- Subnets and networking")
    print("- Security groups and firewalls")
    print "- Virtual machines and instances"
    print("- Load balancers and auto-scaling"
    print("- Storage and databases"
    print("- DNS and routing")

if __name__ == "__main__":
    main()

"""
Example Usage:
1. Install Terraform: https://www.terraform.io/downloads.html
2. Run deployer: python terraform_deployer.py
3. Create and deploy infrastructure projects
4. Manage multi-cloud resources

Key Concepts:
- Infrastructure as Code: Declarative infrastructure definition
- Terraform HCL: HashiCorp Configuration Language
- Multi-Cloud: Support for multiple cloud providers
- State Management: Terraform state tracking
- Resource Dependencies: Resource ordering and relationships

Cloud Providers:
- AWS: Amazon Web Services
- Azure: Microsoft Azure
- GCP: Google Cloud Platform

Resource Types:
- Networking: VPC, subnets, security groups
- Compute: VMs, instances, containers
- Storage: Buckets, disks, databases
- Security: IAM, firewalls, certificates
- Monitoring: Logs, metrics, alerts

Applications:
- Cloud infrastructure provisioning
- Multi-cloud deployments
- Environment management
- Cost optimization
- Compliance automation
- Disaster recovery

Dependencies:
- Terraform: Required for infrastructure operations
- Subprocess: Built-in Python
- JSON: Built-in Python
- YAML: Built-in Python

Best Practices:
- Version control for Terraform code
- Environment-specific configurations
- Remote state management
- Resource tagging and naming
- Security best practices
- Cost monitoring and optimization
- Regular security audits
- Backup and recovery strategies

Legal Note:
- Ensure proper cloud provider credentials
- Monitor cloud costs during deployment
- Follow cloud provider security guidelines
- Clean up resources when done
- Use appropriate IAM permissions
"""
