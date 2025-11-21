pipeline {
    agent any

    environment {
        DOCKERHUB_CREDENTIALS = credentials('dockerhub-cred')
        IMAGE_NAME = "tebancito/ims_cc"
        BUILD_VERSION = "1.0.${BUILD_NUMBER}"
        PROD_TAG = "prod-${new Date().format('yyyyMMdd')}"
    }

    stages {

        stage('Checkout') {
            steps {
                echo "=== Clonando repositorio ==="
                git branch: 'main', url: 'https://github.com/dogor123/IMS_CC'
            }
        }

        stage('Lint Dockerfile') {
            steps {
                echo "=== Ejecutando Dockerfile Lint ==="
                sh "docker run --rm -i hadolint/hadolint < Dockerfile || true"
            }
        }

        stage('Build Docker Image') {
            steps {
                echo "=== Construyendo imagen Docker ==="
                sh """
                    docker build \
                        --build-arg BUILD_VERSION=${BUILD_VERSION} \
                        -t ${IMAGE_NAME}:${BUILD_VERSION} .
                """
            }
        }

        stage('Security Scan') {
            steps {
                echo "=== Ejecutando análisis de seguridad con Trivy ==="
                sh """
                    docker run --rm \
                        -v /var/run/docker.sock:/var/run/docker.sock \
                        aquasec/trivy:latest image --exit-code 0 \
                        --severity HIGH,CRITICAL ${IMAGE_NAME}:${BUILD_VERSION} || true
                """
            }
        }

        stage('Test Container') {
            steps {
                echo "=== Probando contenedor ==="
                sh """
                    docker run -d --name ims_test -p 8090:80 ${IMAGE_NAME}:${BUILD_VERSION}
                    sleep 12
                    curl --fail http://localhost:8090 >/dev/null \
                        || (echo '❌ TEST FALLÓ' && exit 1)
                """
            }
            post {
                always {
                    sh "docker stop ims_test || true"
                    sh "docker rm ims_test || true"
                }
            }
        }

        stage('Login to DockerHub') {
            steps {
                echo "=== Iniciando sesión en DockerHub ==="
                sh """
                    echo ${DOCKERHUB_CREDENTIALS_PSW} | docker login \
                        -u ${DOCKERHUB_CREDENTIALS_USR} --password-stdin
                """
            }
        }

        stage('Push to DockerHub') {
            steps {
                echo "=== Subiendo imágenes a DockerHub ==="
                sh """
                    docker push ${IMAGE_NAME}:${BUILD_VERSION}

                    docker tag ${IMAGE_NAME}:${BUILD_VERSION} ${IMAGE_NAME}:latest
                    docker push ${IMAGE_NAME}:latest

                    docker tag ${IMAGE_NAME}:${BUILD_VERSION} ${IMAGE_NAME}:${PROD_TAG}
                    docker push ${IMAGE_NAME}:${PROD_TAG}
                """
            }
        }
    }

    post {
        always {
            echo "=== Limpieza final ==="
            sh "docker system prune -f || true"
        }
        success {
            echo "🎉 Pipeline completado exitosamente"
        }
        failure {
            echo "❌ Pipeline falló"
        }
    }
}