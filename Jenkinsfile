pipeline {
    agent any

    environment {
        DOCKERHUB_CREDENTIALS = credentials('dockerhub-cred')
        IMAGE_NAME = "tebancito/ims_cc"

        // versión automática basada en BUILD_ID
        BUILD_VERSION = "1.0.${BUILD_NUMBER}"

        // TAG para producción: prod-YYYYMMDD
        PROD_TAG = "prod-${new Date().format('yyyyMMdd')}"
    }

    stages {

        // ===========================
        // 1. CLONE DEL REPOSITORIO
        // ===========================
        stage('Checkout') {
            steps {
                echo "=== Clonando repositorio ==="
                git branch: 'main', url: 'https://github.com/dogor123/IMS_CC'
            }
        }

        // ===========================
        // 2. LINT DEL DOCKERFILE
        // ===========================
        stage('Lint Dockerfile') {
            steps {
                echo "=== Ejecutando Dockerfile Lint ==="
                sh """
                    docker run --rm -i hadolint/hadolint < Dockerfile
                """
            }
        }

        // ===========================
        // 3. SCAN DE SEGURIDAD
        // ===========================
        stage('Security Scan') {
            steps {
                echo "=== Ejecutando análisis de seguridad con Trivy ==="
                sh """
                    docker run --rm -v /var/run/docker.sock:/var/run/docker.sock \
                        aquasec/trivy image --exit-code 0 --severity HIGH,CRITICAL ${IMAGE_NAME}:${BUILD_VERSION} || true
                """
            }
        }

        // ===========================
        // 4. BUILD DE IMAGEN DOCKER
        // ===========================
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

        // ===========================
        // 5. TEST DE LA IMAGEN
        // ===========================
        stage('Test Container') {
            steps {
                echo "=== Probando contenedor ==="
                sh """
                    docker run -d --name ims_test -p 8090:80 ${IMAGE_NAME}:${BUILD_VERSION}
                    echo "Esperando que Apache levante..."
                    sleep 12

                    echo "=== Test HTTP ==="
                    curl --fail http://localhost:8090 >/dev/null \
                        || (echo '❌ TEST FALLÓ: La aplicación no responde' && exit 1)

                    echo '✔ Test de la aplicación PASÓ'
                """
            }
            post {
                always {
                    echo "=== Eliminando contenedor de prueba ==="
                    sh "docker stop ims_test || true"
                    sh "docker rm ims_test || true"
                }
            }
        }

        // ===========================
        // 6. LOGIN A DOCKER HUB
        // ===========================
        stage('Login to DockerHub') {
            steps {
                echo "=== Iniciando sesión en DockerHub ==="
                sh """
                    echo ${DOCKERHUB_CREDENTIALS_PSW} | docker login \
                        -u ${DOCKERHUB_CREDENTIALS_USR} --password-stdin
                """
            }
        }

        // ===========================
        // 7. PUSH A DOCKER HUB
        // ===========================
        stage('Push to DockerHub') {
            steps {
                echo "=== Subiendo imagen a DockerHub ==="
                sh """
                    docker push ${IMAGE_NAME}:${BUILD_VERSION}

                    docker tag ${IMAGE_NAME}:${BUILD_VERSION} ${IMAGE_NAME}:latest
                    docker push ${IMAGE_NAME}:latest

                    docker tag ${IMAGE_NAME}:${BUILD_VERSION} ${IMAGE_NAME}:${PROD_TAG}
                    docker push ${IMAGE_NAME}:${PROD_TAG}
                """
            }
        }

        // ===========================
        // 8. DEPLOY A SERVIDOR (OPCIONAL)
        // ===========================
        stage('Remote Deploy (optional)') {
            when {
                expression { return false }  // cámbialo a true cuando tengas un servidor remoto
            }
            steps {
                echo "=== Desplegando en servidor remoto ==="
                sh """
                    ssh user@SERVER-IP 'docker pull ${IMAGE_NAME}:${PROD_TAG} &&
                                        docker compose down &&
                                        docker compose up -d'
                """
            }
        }
    }

    // ===========================
    // POST-PIPELINE
    // ===========================
    post {
        always {
            echo "=== Limpieza final ==="
            sh 'docker system prune -f || true'
        }
        success {
            echo "🎉 Pipeline completado exitosamente"
        }
        failure {
            echo "❌ Pipeline falló"
        }
    }
}