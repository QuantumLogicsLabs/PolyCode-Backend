"""
Docker Container Manager
=========================

Comprehensive Docker container management tool.
Demonstrates container operations, image management, and orchestration.
"""

import docker
import json
import time
import os
from typing import List, Dict, Optional, Tuple
from datetime import datetime
from dataclasses import dataclass, asdict
import yaml
import subprocess
import logging

@dataclass
class ContainerInfo:
    """Container information structure"""
    id: str
    name: str
    image: str
    status: str
    ports: Dict[str, str]
    created: datetime
    labels: Dict[str, str]
    environment: Dict[str, str]
    volumes: List[str]
    networks: List[str]

@dataclass
class ImageInfo:
    """Docker image information structure"""
    id: str
    repo_tags: List[str]
    size: int
    created: datetime
    labels: Dict[str, str]

class DockerManager:
    """Docker container and image management"""
    
    def __init__(self):
        try:
            self.client = docker.from_env()
            self.client.ping()
            self.connected = True
        except Exception as e:
            print(f"Error connecting to Docker: {e}")
            self.connected = False
    
    def list_containers(self, all_containers: bool = False) -> List[ContainerInfo]:
        """List all containers"""
        if not self.connected:
            return []
        
        try:
            containers = self.client.containers.list(all=all_containers)
            container_list = []
            
            for container in containers:
                info = ContainerInfo(
                    id=container.id[:12],
                    name=container.name,
                    image=container.image.tags[0] if container.image.tags else container.image.id[:12],
                    status=container.status,
                    ports=container.ports,
                    created=container.attrs['Created'],
                    labels=container.labels,
                    environment=container.attrs['Config']['Env'] or [],
                    volumes=list(container.attrs['Mounts']) if container.attrs['Mounts'] else [],
                    networks=list(container.attrs['NetworkSettings']['Networks'].keys()) if 'NetworkSettings' in container.attrs and 'Networks' in container.attrs['NetworkSettings'] else []
                )
                container_list.append(info)
            
            return container_list
        
        except Exception as e:
            print(f"Error listing containers: {e}")
            return []
    
    def list_images(self) -> List[ImageInfo]:
        """List all Docker images"""
        if not self.connected:
            return []
        
        try:
            images = self.client.images.list()
            image_list = []
            
            for image in images:
                info = ImageInfo(
                    id=image.id[:12],
                    repo_tags=image.tags,
                    size=image.attrs['Size'],
                    created=datetime.fromisoformat(image.attrs['Created'].replace('Z', '+00:00')),
                    labels=image.attrs['Config'].get('Labels', {})
                )
                image_list.append(info)
            
            return image_list
        
        except Exception as e:
            print(f"Error listing images: {e}")
            return []
    
    def run_container(self, image: str, name: str = None, ports: Dict[str, str] = None,
                     environment: Dict[str, str] = None, volumes: Dict[str, str] = None,
                     detach: bool = True, auto_remove: bool = False) -> Optional[str]:
        """Run a new container"""
        if not self.connected:
            return None
        
        try:
            # Prepare port mappings
            port_bindings = None
            if ports:
                port_bindings = ports
            
            # Prepare volume mappings
            volume_bindings = None
            if volumes:
                volume_bindings = volumes
            
            # Run container
            container = self.client.containers.run(
                image=image,
                name=name,
                ports=port_bindings,
                environment=environment,
                volumes=volume_bindings,
                detach=detach,
                auto_remove=auto_remove
            )
            
            print(f"Container started: {container.id[:12]}")
            return container.id[:12]
        
        except Exception as e:
            print(f"Error running container: {e}")
            return None
    
    def stop_container(self, container_id: str) -> bool:
        """Stop a running container"""
        if not self.connected:
            return False
        
        try:
            container = self.client.containers.get(container_id)
            container.stop()
            print(f"Container stopped: {container_id}")
            return True
        
        except Exception as e:
            print(f"Error stopping container {container_id}: {e}")
            return False
    
    def remove_container(self, container_id: str, force: bool = False) -> bool:
        """Remove a container"""
        if not self.connected:
            return False
        
        try:
            container = self.client.containers.get(container_id)
            container.remove(force=force)
            print(f"Container removed: {container_id}")
            return True
        
        except Exception as e:
            print(f"Error removing container {container_id}: {e}")
            return False
    
    def build_image(self, dockerfile_path: str, tag: str, context_path: str = ".") -> Optional[str]:
        """Build a Docker image"""
        if not self.connected:
            return None
        
        try:
            # Check if dockerfile exists
            dockerfile = os.path.join(context_path, dockerfile_path)
            if not os.path.exists(dockerfile):
                print(f"Dockerfile not found: {dockerfile}")
                return None
            
            # Build image
            image, build_logs = self.client.images.build(
                path=context_path,
                dockerfile=dockerfile_path,
                tag=tag,
                rm=True
            )
            
            print(f"Image built: {image.id[:12]}")
            return image.id[:12]
        
        except Exception as e:
            print(f"Error building image: {e}")
            return None
    
    def pull_image(self, image_name: str) -> bool:
        """Pull a Docker image"""
        if not self.connected:
            return False
        
        try:
            print(f"Pulling image: {image_name}")
            self.client.images.pull(image_name)
            print(f"Image pulled successfully: {image_name}")
            return True
        
        except Exception as e:
            print(f"Error pulling image {image_name}: {e}")
            return False
    
    def get_container_logs(self, container_id: str, tail: int = 100) -> str:
        """Get container logs"""
        if not self.connected:
            return ""
        
        try:
            container = self.client.containers.get(container_id)
            logs = container.logs(tail=tail).decode('utf-8')
            return logs
        
        except Exception as e:
            print(f"Error getting logs for {container_id}: {e}")
            return ""
    
    def get_container_stats(self, container_id: str) -> Dict:
        """Get container resource usage statistics"""
        if not self.connected:
            return {}
        
        try:
            container = self.client.containers.get(container_id)
            stats = container.stats(stream=False)
            
            # Parse CPU usage
            cpu_usage = 0.0
            if stats['cpu_stats']['cpu_usage']['total_usage'] > 0:
                cpu_delta = stats['cpu_stats']['cpu_usage']['total_usage'] - stats['precpu_stats']['cpu_usage']['total_usage']
                system_delta = stats['cpu_stats']['system_cpu_usage'] - stats['precpu_stats']['system_cpu_usage']
                if system_delta > 0:
                    cpu_usage = (cpu_delta / system_delta) * len(stats['cpu_stats']['cpu_usage'].get('percpu_usage', [1])) * 100
            
            # Parse memory usage
            memory_usage = stats['memory_stats']['usage']
            memory_limit = stats['memory_stats']['limit']
            memory_percent = (memory_usage / memory_limit) * 100 if memory_limit > 0 else 0
            
            return {
                'cpu_percent': round(cpu_usage, 2),
                'memory_usage': memory_usage,
                'memory_limit': memory_limit,
                'memory_percent': round(memory_percent, 2),
                'network_rx': stats['networks']['eth0']['rx_bytes'] if 'networks' in stats and 'eth0' in stats['networks'] else 0,
                'network_tx': stats['networks']['eth0']['tx_bytes'] if 'networks' in stats and 'eth0' in stats['networks'] else 0
            }
        
        except Exception as e:
            print(f"Error getting stats for {container_id}: {e}")
            return {}
    
    def create_dockerfile(self, base_image: str, app_files: List[str], 
                         commands: List[str], expose_port: int = None,
                         workdir: str = "/app") -> str:
        """Generate a Dockerfile"""
        dockerfile_content = []
        
        # Base image
        dockerfile_content.append(f"FROM {base_image}")
        
        # Set working directory
        if workdir:
            dockerfile_content.append(f"WORKDIR {workdir}")
        
        # Copy application files
        for file_path in app_files:
            dockerfile_content.append(f"COPY {file_path} {workdir}")
        
        # Run commands
        for command in commands:
            dockerfile_content.append(f"RUN {command}")
        
        # Expose port
        if expose_port:
            dockerfile_content.append(f"EXPOSE {expose_port}")
        
        # Default command
        dockerfile_content.append('CMD ["python", "app.py"]')
        
        return "\n".join(dockerfile_content)
    
    def save_dockerfile(self, dockerfile_content: str, file_path: str = "Dockerfile"):
        """Save Dockerfile to file"""
        with open(file_path, 'w') as f:
            f.write(dockerfile_content)
        print(f"Dockerfile saved to: {file_path}")
    
    def create_docker_compose(self, services: Dict[str, Dict]) -> str:
        """Generate docker-compose.yml"""
        compose_data = {
            'version': '3.8',
            'services': services
        }
        
        return yaml.dump(compose_data, default_flow_style=False)
    
    def save_docker_compose(self, compose_content: str, file_path: str = "docker-compose.yml"):
        """Save docker-compose.yml to file"""
        with open(file_path, 'w') as f:
            f.write(compose_content)
        print(f"docker-compose.yml saved to: {file_path}")
    
    def cleanup_resources(self) -> Dict:
        """Clean up unused Docker resources"""
        if not self.connected:
            return {}
        
        cleanup_results = {
            'containers_removed': 0,
            'images_removed': 0,
            'volumes_removed': 0,
            'networks_removed': 0
        }
        
        try:
            # Remove stopped containers
            stopped_containers = self.client.containers.list(all=True, filters={'status': 'exited'})
            for container in stopped_containers:
                container.remove()
                cleanup_results['containers_removed'] += 1
            
            # Remove dangling images
            dangling_images = self.client.images.list(filters={'dangling': True})
            for image in dangling_images:
                self.client.images.remove(image.id, force=True)
                cleanup_results['images_removed'] += 1
            
            # Remove unused volumes
            unused_volumes = self.client.volumes.list(filters={'dangling': True})
            for volume in unused_volumes:
                volume.remove()
                cleanup_results['volumes_removed'] += 1
            
            # Remove unused networks
            unused_networks = self.client.networks.list(filters={'dangling': True})
            for network in unused_networks:
                network.remove()
                cleanup_results['networks_removed'] += 1
            
            print("Docker cleanup completed")
            return cleanup_results
        
        except Exception as e:
            print(f"Error during cleanup: {e}")
            return cleanup_results
    
    def export_container_image(self, container_id: str, output_file: str) -> bool:
        """Export container image to tar file"""
        if not self.connected:
            return False
        
        try:
            container = self.client.containers.get(container_id)
            image = container.image
            
            # Save image to tar file
            with open(output_file, 'wb') as f:
                for chunk in image.save():
                    f.write(chunk)
            
            print(f"Image exported to: {output_file}")
            return True
        
        except Exception as e:
            print(f"Error exporting image: {e}")
            return False
    
    def import_container_image(self, image_file: str) -> Optional[str]:
        """Import container image from tar file"""
        if not self.connected:
            return None
        
        try:
            with open(image_file, 'rb') as f:
                images = self.client.images.load(f)
            
            if images:
                image_id = images[0].id[:12]
                print(f"Image imported: {image_id}")
                return image_id
            
            return None
        
        except Exception as e:
            print(f"Error importing image: {e}")
            return None

def create_sample_app():
    """Create a sample Python web application"""
    app_code = '''
from flask import Flask, jsonify
import os
import time

app = Flask(__name__)

@app.route('/')
def home():
    return jsonify({
        'message': 'Hello from Docker!',
        'container_id': os.environ.get('HOSTNAME', 'unknown'),
        'timestamp': time.time()
    })

@app.route('/health')
def health():
    return jsonify({'status': 'healthy'})

@app.route('/info')
def info():
    return jsonify({
        'python_version': os.sys.version,
        'environment': dict(os.environ)
    })

if __name__ == '__main__':
    app.run(host='0.0.0.0', port=5000, debug=True)
'''
    
    requirements = '''
Flask==2.3.3
Werkzeug==2.3.7
'''
    
    with open('app.py', 'w') as f:
        f.write(app_code)
    
    with open('requirements.txt', 'w') as f:
        f.write(requirements)
    
    print("Sample Flask application created")

def main():
    """Main function to demonstrate Docker manager"""
    print("=== Docker Container Manager ===\n")
    
    manager = DockerManager()
    
    if not manager.connected:
        print("Docker is not running or not accessible.")
        print("Please ensure Docker is installed and running.")
        return
    
    print("Docker connection established!")
    
    # Show current containers
    print("\n1. Current Containers:")
    containers = manager.list_containers(all_containers=True)
    
    if containers:
        for container in containers:
            print(f"  {container.id[:12]} - {container.name} - {container.status}")
    else:
        print("  No containers found")
    
    # Show current images
    print("\n2. Current Images:")
    images = manager.list_images()
    
    if images:
        for image in images[:10]:  # Show first 10
            tags = ', '.join(image.repo_tags) if image.repo_tags else 'no-tags'
            size_mb = image.size / (1024 * 1024)
            print(f"  {image.id[:12]} - {tags} - {size_mb:.1f}MB")
    else:
        print("  No images found")
    
    # Create sample application
    print("\n3. Creating Sample Application:")
    create_sample_app()
    
    # Generate Dockerfile
    print("\n4. Generating Dockerfile:")
    dockerfile_content = manager.create_dockerfile(
        base_image="python:3.9-slim",
        app_files=["app.py", "requirements.txt"],
        commands=["pip install -r requirements.txt"],
        expose_port=5000
    )
    
    manager.save_dockerfile(dockerfile_content)
    
    # Build image
    print("\n5. Building Docker Image:")
    image_id = manager.build_image("Dockerfile", "sample-flask-app")
    
    if image_id:
        print(f"Image built successfully: {image_id}")
        
        # Run container
        print("\n6. Running Container:")
        container_id = manager.run_container(
            image="sample-flask-app",
            name="sample-flask",
            ports={'5000/tcp': '5001'},
            environment={'FLASK_ENV': 'development'},
            detach=True
        )
        
        if container_id:
            print(f"Container started: {container_id}")
            
            # Wait a moment for container to start
            time.sleep(3)
            
            # Get container logs
            print("\n7. Container Logs:")
            logs = manager.get_container_logs(container_id)
            print(logs)
            
            # Get container stats
            print("\n8. Container Statistics:")
            stats = manager.get_container_stats(container_id)
            if stats:
                print(f"  CPU Usage: {stats['cpu_percent']}%")
                print(f"  Memory Usage: {stats['memory_percent']}%")
                print(f"  Network RX: {stats['network_rx']} bytes")
                print(f"  Network TX: {stats['network_tx']} bytes")
            
            # Stop and remove container
            print("\n9. Stopping Container:")
            manager.stop_container(container_id)
            manager.remove_container(container_id)
    
    # Create docker-compose file
    print("\n10. Creating Docker Compose:")
    services = {
        'web': {
            'build': '.',
            'ports': ['5000:5000'],
            'environment': ['FLASK_ENV=development'],
            'volumes': ['./app.py:/app/app.py']
        },
        'redis': {
            'image': 'redis:alpine',
            'ports': ['6379:6379']
        }
    }
    
    compose_content = manager.create_docker_compose(services)
    manager.save_docker_compose(compose_content)
    
    # Cleanup
    print("\n11. Cleanup Resources:")
    cleanup_results = manager.cleanup_resources()
    
    for resource_type, count in cleanup_results.items():
        print(f"  {resource_type}: {count}")
    
    # Interactive menu
    print("\n=== Docker Manager Interactive ===")
    
    while True:
        print("\nOptions:")
        print("1. List containers")
        print("2. List images")
        print("3. Run container")
        print("4. Stop container")
        print("5. Remove container")
        print("6. Pull image")
        print("7. Build image")
        print("8. Get container logs")
        print("9. Get container stats")
        print("0. Exit")
        
        choice = input("\nSelect option: ").strip()
        
        if choice == "0":
            break
        
        elif choice == "1":
            containers = manager.list_containers(all_containers=True)
            for container in containers:
                print(f"{container.id[:12]} - {container.name} - {container.status}")
        
        elif choice == "2":
            images = manager.list_images()
            for image in images:
                tags = ', '.join(image.repo_tags) if image.repo_tags else 'no-tags'
                print(f"{image.id[:12]} - {tags}")
        
        elif choice == "3":
            image = input("Enter image name: ").strip()
            name = input("Enter container name (optional): ").strip() or None
            ports_input = input("Enter port mappings (host:container, comma-separated): ").strip()
            
            ports = {}
            if ports_input:
                for port_map in ports_input.split(','):
                    if ':' in port_map:
                        host_port, container_port = port_map.strip().split(':')
                        ports[f"{container_port}/tcp"] = host_port
            
            container_id = manager.run_container(image, name, ports)
            if container_id:
                print(f"Container started: {container_id}")
        
        elif choice == "4":
            container_id = input("Enter container ID: ").strip()
            manager.stop_container(container_id)
        
        elif choice == "5":
            container_id = input("Enter container ID: ").strip()
            force = input("Force removal? (y/n): ").strip().lower() == 'y'
            manager.remove_container(container_id, force)
        
        elif choice == "6":
            image = input("Enter image name to pull: ").strip()
            manager.pull_image(image)
        
        elif choice == "7":
            dockerfile = input("Enter Dockerfile path: ").strip()
            tag = input("Enter image tag: ").strip()
            context = input("Enter build context path (default: .): ").strip() or "."
            image_id = manager.build_image(dockerfile, tag, context)
            if image_id:
                print(f"Image built: {image_id}")
        
        elif choice == "8":
            container_id = input("Enter container ID: ").strip()
            logs = manager.get_container_logs(container_id)
            print(logs)
        
        elif choice == "9":
            container_id = input("Enter container ID: ").strip()
            stats = manager.get_container_stats(container_id)
            if stats:
                print(f"CPU: {stats['cpu_percent']}%")
                print(f"Memory: {stats['memory_percent']}%")
                print(f"Network RX: {stats['network_rx']} bytes")
                print(f"Network TX: {stats['network_tx']} bytes")
        
        else:
            print("Invalid option")
    
    print("\n=== Docker Manager Demo Completed ===")
    print("Features demonstrated:")
    print("- Container management (list, run, stop, remove)")
    print("- Image management (list, build, pull)")
    print("- Container monitoring (logs, stats)")
    print("- Dockerfile generation")
    print("- Docker Compose configuration")
    print("- Resource cleanup")
    print("- Interactive container operations")

if __name__ == "__main__":
    main()

"""
Example Usage:
1. Install Docker and Python Docker SDK: pip install docker pyyaml
2. Ensure Docker daemon is running
3. Run manager: python docker_manager.py
4. Follow interactive menu for container operations

Key Concepts:
- Container Lifecycle: Create, run, stop, remove
- Image Management: Build, pull, list images
- Resource Monitoring: CPU, memory, network usage
- Volume Management: Data persistence
- Network Configuration: Port mapping, networking
- Docker Compose: Multi-container orchestration

Operations:
- Container Operations: run, stop, remove, logs, stats
- Image Operations: build, pull, list, export, import
- Resource Management: cleanup, monitoring
- Configuration: Dockerfile, docker-compose generation

Applications:
- Application deployment
- Microservices management
- Development environment setup
- Continuous integration/deployment
- Resource optimization
- Container orchestration

Dependencies:
- Docker Engine: Required for container operations
- Python Docker SDK: pip install docker
- PyYAML: pip install pyyaml
- Flask: For sample application

Security Considerations:
- Use official base images
- Regular security scanning
- Proper user permissions
- Network isolation
- Resource limits
- Secret management
"""
