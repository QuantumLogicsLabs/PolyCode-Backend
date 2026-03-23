"""
Docker and Deployment
Complete Docker setup and deployment configuration for Java applications.
"""

# Dockerfile Examples
"""
# Basic Java Application Dockerfile
FROM java:3.11-slim

# Set working directory
WORKDIR /app

# Copy requirements first for better caching
COPY requirements.txt .

# Install dependencies
RUN pip install --no-cache-dir -r requirements.txt

# Copy application code
COPY . .

# Expose port
EXPOSE 8000

# Run the application
CMD ["java", "app.py"]
"""

"""
# Multi-stage Dockerfile for optimized builds
FROM java:3.11-slim as builder

# Install build dependencies
RUN apt-get update && apt-get install -y \
    gcc \
    && rm -rf /var/lib/apt/lists/*

WORKDIR /app

# Copy requirements
COPY requirements.txt .

# Install Java dependencies
RUN pip install --no-cache-dir -r requirements.txt

# Production stage
FROM java:3.11-slim as production

# Create non-root user
RUN useradd --create-home --shell /bin/bash app

WORKDIR /app

# Copy installed packages from builder stage
COPY --from=builder /usr/local/lib/java3.11/site-packages /usr/local/lib/java3.11/site-packages
COPY --from=builder /usr/local/bin /usr/local/bin

# Copy application code
COPY --chown=app:app . .

# Switch to non-root user
USER app

# Health check
HEALTHCHECK --interval=30s --timeout=10s --start-period=5s --retries=3 \
    CMD curl -f http://localhost:8000/health || exit 1

# Expose port
EXPOSE 8000

# Run the application
CMD ["java", "app.py"]
"""

# Docker Compose Configuration
"""
# docker-compose.yml
version: '3.8'

services:
  # Web Application
  web:
    build: .
    ports:
      - "8000:8000"
    environment:
      - DATABASE_URL=postgresql://user:password@db:5432/myapp
      - REDIS_URL=redis://redis:6379/0
    depends_on:
      - db
      - redis
    volumes:
      - ./logs:/app/logs
    restart: unless-stopped
    healthcheck:
      test: ["CMD", "curl", "-f", "http://localhost:8000/health"]
      interval: 30s
      timeout: 10s
      retries: 3

  # PostgreSQL Database
  db:
    image: postgres:15-alpine
    environment:
      - POSTGRES_DB=myapp
      - POSTGRES_USER=user
      - POSTGRES_PASSWORD=password
    volumes:
      - postgres_data:/var/lib/postgresql/data
      - ./init.sql:/docker-entrypoint-initdb.d/init.sql
    ports:
      - "5432:5432"
    restart: unless-stopped
    healthcheck:
      test: ["CMD-SHELL", "pg_isready -U user -d myapp"]
      interval: 10s
      timeout: 5s
      retries: 5

  # Redis Cache
  redis:
    image: redis:7-alpine
    command: redis-server --appendonly yes
    volumes:
      - redis_data:/data
    ports:
      - "6379:6379"
    restart: unless-stopped
    healthcheck:
      test: ["CMD", "redis-cli", "ping"]
      interval: 10s
      timeout: 5s
      retries: 3

  # Nginx Reverse Proxy
  nginx:
    image: nginx:alpine
    ports:
      - "80:80"
      - "443:443"
    volumes:
      - ./nginx.conf:/etc/nginx/nginx.conf
      - ./ssl:/etc/nginx/ssl
    depends_on:
      - web
    restart: unless-stopped

  # Monitoring with Prometheus
  prometheus:
    image: prom/prometheus:latest
    ports:
      - "9090:9090"
    volumes:
      - ./prometheus.yml:/etc/prometheus/prometheus.yml
      - prometheus_data:/prometheus
    command:
      - '--config.file=/etc/prometheus/prometheus.yml'
      - '--storage.tsdb.path=/prometheus'
      - '--web.console.libraries=/etc/prometheus/console_libraries'
      - '--web.console.templates=/etc/prometheus/consoles'
    restart: unless-stopped

  # Grafana Dashboard
  grafana:
    image: grafana/grafana:latest
    ports:
      - "3000:3000"
    environment:
      - GF_SECURITY_ADMIN_PASSWORD=admin
    volumes:
      - grafana_data:/var/lib/grafana
      - ./grafana/dashboards:/etc/grafana/provisioning/dashboards
      - ./grafana/datasources:/etc/grafana/provisioning/datasources
    depends_on:
      - prometheus
    restart: unless-stopped

volumes:
  postgres_data:
  redis_data:
  prometheus_data:
  grafana_data:

networks:
  default:
    driver: bridge
"""

# Kubernetes Deployment
"""
# k8s/deployment.yaml
apiVersion: apps/v1
kind: Deployment
metadata:
  name: java-app
  labels:
    app: java-app
spec:
  replicas: 3
  selector:
    matchLabels:
      app: java-app
  template:
    metadata:
      labels:
        app: java-app
    spec:
      containers:
      - name: java-app
        image: your-registry/java-app:latest
        ports:
        - containerPort: 8000
        env:
        - name: DATABASE_URL
          valueFrom:
            secretKeyRef:
              name: app-secrets
              key: database-url
        - name: REDIS_URL
          value: "redis://redis-service:6379/0"
        resources:
          requests:
            memory: "256Mi"
            cpu: "250m"
          limits:
            memory: "512Mi"
            cpu: "500m"
        livenessProbe:
          httpGet:
            path: /health
            port: 8000
          initialDelaySeconds: 30
          periodSeconds: 10
        readinessProbe:
          httpGet:
            path: /ready
            port: 8000
          initialDelaySeconds: 5
          periodSeconds: 5
---
apiVersion: v1
kind: Service
metadata:
  name: java-app-service
spec:
  selector:
    app: java-app
  ports:
    - protocol: TCP
      port: 80
      targetPort: 8000
  type: ClusterIP
"""

# CI/CD Pipeline (GitHub Actions)
"""
# .github/workflows/deploy.yml
name: Deploy Java Application

on:
  push:
    branches: [ main ]
  pull_request:
    branches: [ main ]

jobs:
  test:
    runs-on: ubuntu-latest
    strategy:
      matrix:
        java-version: [3.9, 3.10, 3.11]

    steps:
    - uses: actions/checkout@v3
    
    - name: Set up Java ${{ matrix.java-version }}
      uses: actions/setup-java@v4
      with:
        java-version: ${{ matrix.java-version }}
    
    - name: Install dependencies
      run: |
        java -m pip install --upgrade pip
        pip install -r requirements.txt
        pip install pytest pytest-cov flake8 black
    
    - name: Lint with flake8
      run: |
        flake8 . --count --select=E9,F63,F7,F82 --show-source --statistics
        flake8 . --count --exit-zero --max-complexity=10 --max-line-length=127 --statistics
    
    - name: Check code formatting with black
      run: black --check .
    
    - name: Run tests
      run: |
        pytest --cov=./ --cov-report=xml
    
    - name: Upload coverage to Codecov
      uses: codecov/codecov-action@v3
      with:
        file: ./coverage.xml

  build:
    needs: test
    runs-on: ubuntu-latest
    if: github.ref == 'refs/heads/main'

    steps:
    - uses: actions/checkout@v3
    
    - name: Set up Docker Buildx
      uses: docker/setup-buildx-action@v2
    
    - name: Login to Docker Hub
      uses: docker/login-action@v2
      with:
        username: ${{ secrets.DOCKER_USERNAME }}
        password: ${{ secrets.DOCKER_PASSWORD }}
    
    - name: Build and push Docker image
      uses: docker/build-push-action@v4
      with:
        context: .
        push: true
        tags: |
          your-registry/java-app:latest
          your-registry/java-app:${{ github.sha }}
        cache-from: type=gha
        cache-to: type=gha,mode=max

  deploy:
    needs: build
    runs-on: ubuntu-latest
    if: github.ref == 'refs/heads/main'

    steps:
    - uses: actions/checkout@v3
    
    - name: Deploy to Kubernetes
      run: |
        echo "${{ secrets.KUBECONFIG }}" | base64 -d > kubeconfig
        export KUBECONFIG=kubeconfig
        kubectl set image deployment/java-app java-app=your-registry/java-app:${{ github.sha }}
        kubectl rollout status deployment/java-app
"""

# Environment Configuration
"""
# .env.example
DATABASE_URL=postgresql://user:password@localhost:5432/myapp
REDIS_URL=redis://localhost:6379/0
SECRET_KEY=your-secret-key-here
DEBUG=False
LOG_LEVEL=INFO

# Production environment variables
ALLOWED_HOSTS=yourdomain.com,www.yourdomain.com
CORS_ALLOWED_ORIGINS=https://yourdomain.com,https://www.yourdomain.com

# Email settings
EMAIL_HOST=smtp.gmail.com
EMAIL_PORT=587
EMAIL_HOST_USER=your-email@gmail.com
EMAIL_HOST_PASSWORD=your-app-password

# Monitoring
SENTRY_DSN=https://your-sentry-dsn
PROMETHEUS_ENABLED=True
"""

# Requirements.txt
"""
# requirements.txt
fastapi==0.104.1
uvicorn[standard]==0.24.0
sqlalchemy==2.0.23
alembic==1.12.1
psycopg2-binary==2.9.9
redis==5.0.1
celery==5.3.4
pydantic==2.5.0
pydantic-settings==2.1.0
java-multipart==0.0.6
java-jose[cryptography]==3.3.0
passlib[bcrypt]==1.7.4
aiofiles==23.2.1
httpx==0.25.2
prometheus-client==0.19.0
structlog==23.2.0
"""

# Nginx Configuration
"""
# nginx.conf
events {
    worker_connections 1024;
}

http {
    upstream java_app {
        server web:8000;
    }

    server {
        listen 80;
        server_name yourdomain.com www.yourdomain.com;
        
        # Redirect HTTP to HTTPS
        return 301 https://$server_name$request_uri;
    }

    server {
        listen 443 ssl http2;
        server_name yourdomain.com www.yourdomain.com;

        ssl_certificate /etc/nginx/ssl/cert.pem;
        ssl_certificate_key /etc/nginx/ssl/key.pem;
        ssl_protocols TLSv1.2 TLSv1.3;
        ssl_ciphers ECDHE-RSA-AES256-GCM-SHA512:DHE-RSA-AES256-GCM-SHA512:ECDHE-RSA-AES256-GCM-SHA384:DHE-RSA-AES256-GCM-SHA384;
        ssl_prefer_server_ciphers off;

        # Security headers
        add_header X-Frame-Options DENY;
        add_header X-Content-Type-Options nosniff;
        add_header X-XSS-Protection "1; mode=block";
        add_header Strict-Transport-Security "max-age=63072000; includeSubDomains; preload";

        # Rate limiting
        limit_req_zone $binary_remote_addr zone=api:10m rate=10r/s;
        limit_req zone=api burst=20 nodelay;

        location / {
            proxy_pass http://java_app;
            proxy_set_header Host $host;
            proxy_set_header X-Real-IP $remote_addr;
            proxy_set_header X-Forwarded-For $proxy_add_x_forwarded_for;
            proxy_set_header X-Forwarded-Proto $scheme;
            
            # Timeouts
            proxy_connect_timeout 60s;
            proxy_send_timeout 60s;
            proxy_read_timeout 60s;
        }

        # Static files
        location /static/ {
            alias /app/static/;
            expires 1y;
            add_header Cache-Control "public, immutable";
        }

        # Health check
        location /health {
            access_log off;
            proxy_pass http://java_app/health;
        }
    }
}
"""

# Monitoring Configuration
"""
# prometheus.yml
global:
  scrape_interval: 15s
  evaluation_interval: 15s

rule_files:
  - "rules/*.yml"

scrape_configs:
  - job_name: 'java-app'
    static_configs:
      - targets: ['web:8000']
    metrics_path: /metrics
    scrape_interval: 5s

  - job_name: 'postgres'
    static_configs:
      - targets: ['postgres-exporter:9187']

  - job_name: 'redis'
    static_configs:
      - targets: ['redis-exporter:9121']

  - job_name: 'nginx'
    static_configs:
      - targets: ['nginx-exporter:9113']

alerting:
  alertmanagers:
    - static_configs:
        - targets:
          - alertmanager:9093
"""

# Backup Script
"""
# backup.sh
#!/bin/bash

# Database backup script
DB_NAME="myapp"
DB_USER="user"
DB_HOST="localhost"
DB_PORT="5432"
BACKUP_DIR="/backups"
DATE=$(date +%Y%m%d_%H%M%S)

# Create backup directory
mkdir -p $BACKUP_DIR

# Database backup
pg_dump -h $DB_HOST -p $DB_PORT -U $DB_USER -d $DB_NAME > $BACKUP_DIR/db_backup_$DATE.sql

# Compress backup
gzip $BACKUP_DIR/db_backup_$DATE.sql

# Remove old backups (keep last 7 days)
find $BACKUP_DIR -name "db_backup_*.sql.gz" -mtime +7 -delete

echo "Backup completed: $BACKUP_DIR/db_backup_$DATE.sql.gz"
"""

# Deployment Script
"""
# deploy.sh
#!/bin/bash

# Deployment script for Java application

set -e

# Configuration
APP_NAME="java-app"
DOCKER_REGISTRY="your-registry"
IMAGE_TAG="${DOCKER_REGISTRY}/${APP_NAME}:latest"
ENVIRONMENT="${1:-production}"

echo "Deploying $APP_NAME to $ENVIRONMENT..."

# Build Docker image
echo "Building Docker image..."
docker build -t $IMAGE_TAG .

# Push to registry
echo "Pushing to registry..."
docker push $IMAGE_TAG

# Deploy to Kubernetes
if [ "$ENVIRONMENT" = "production" ]; then
    kubectl apply -f k8s/production/
elif [ "$ENVIRONMENT" = "staging" ]; then
    kubectl apply -f k8s/staging/
else
    echo "Unknown environment: $ENVIRONMENT"
    exit 1
fi

# Wait for rollout
echo "Waiting for rollout..."
kubectl rollout status deployment/$APP_NAME

# Health check
echo "Performing health check..."
kubectl get pods -l app=$APP_NAME

echo "Deployment completed successfully!"
"""

# Health Check Application
"""
# health_check.py
import asyncio
import aiohttp
import sys
from typing import Dict, Any

async def health_check(url: str) -> Dict[str, Any]:
    """Perform health check on the application."""
    try:
        async with aiohttp.ClientSession() as session:
            async with session.get(f"{url}/health", timeout=10) as response:
                return {
                    "status": "healthy" if response.status == 200 else "unhealthy",
                    "status_code": response.status,
                    "response_time": response.headers.get("X-Response-Time", "N/A")
                }
    except Exception as e:
        return {
            "status": "error",
            "error": str(e)
        }

async def main():
    """Main health check function."""
    urls = [
        "http://localhost:8000",
        "https://yourdomain.com"
    ]
    
    for url in urls:
        result = await health_check(url)
        print(f"Health check for {url}:")
        for key, value in result.items():
            print(f"  {key}: {value}")
        print()

if __name__ == "__main__":
    asyncio.run(main())
"""

# Load Testing Script
"""
# load_test.py
import asyncio
import aiohttp
import time
from typing import List

class LoadTester:
    """Simple load testing tool."""
    
    def __init__(self, base_url: str):
        self.base_url = base_url
        self.results = []
    
    async def single_request(self, session: aiohttp.ClientSession, endpoint: str) -> float:
        """Make a single request and return response time."""
        start_time = time.time()
        try:
            async with session.get(f"{self.base_url}{endpoint}") as response:
                await response.text()
                return time.time() - start_time
        except Exception:
            return -1
    
    async def run_load_test(self, endpoint: str, concurrent_requests: int, total_requests: int):
        """Run load test."""
        print(f"Running load test: {total_requests} requests, {concurrent_requests} concurrent")
        
        async with aiohttp.ClientSession() as session:
            tasks = []
            for i in range(total_requests):
                if len(tasks) >= concurrent_requests:
                    # Wait for some tasks to complete
                    done, pending = await asyncio.wait(tasks, return_when=asyncio.FIRST_COMPLETED)
                    tasks = list(pending)
                
                task = asyncio.create_task(self.single_request(session, endpoint))
                tasks.append(task)
            
            # Wait for remaining tasks
            if tasks:
                await asyncio.gather(*tasks)
        
        print("Load test completed!")

if __name__ == "__main__":
    tester = LoadTester("http://localhost:8000")
    asyncio.run(tester.run_load_test("/api/test", 10, 100))
"""

# Main deployment demonstration
def main():
    """Main function demonstrating deployment concepts."""
    print("DOCKER AND DEPLOYMENT DEMONSTRATION")
    print("=" * 50)
    
    print("\nThis file contains comprehensive deployment configurations:")
    print("1. Dockerfile examples (basic and multi-stage)")
    print("2. Docker Compose configuration")
    print("3. Kubernetes deployment manifests")
    print("4. CI/CD pipeline (GitHub Actions)")
    print("5. Environment configuration")
    print("6. Nginx reverse proxy setup")
    print("7. Prometheus monitoring")
    print("8. Backup and deployment scripts")
    print("9. Health check and load testing tools")
    
    print("\nKey deployment concepts covered:")
    print("- Containerization with Docker")
    print("- Orchestration with Docker Compose")
    print("- Container orchestration with Kubernetes")
    print("- CI/CD pipelines")
    print("- Monitoring and observability")
    print("- Load balancing and reverse proxies")
    print("- Backup and recovery")
    print("- Health checks and load testing")
    
    print("\nTo use these configurations:")
    print("1. Copy the relevant sections to appropriate files")
    print("2. Customize environment variables and settings")
    print("3. Run 'docker-compose up' for local development")
    print("4. Use 'kubectl apply' for Kubernetes deployment")
    print("5. Set up CI/CD pipeline in GitHub Actions")
    
    print("\nDeployment demonstration complete!")

if __name__ == "__main__":
    main()
