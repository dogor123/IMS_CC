pipeline {
    agent any

    environment {
        DOCKERHUB_CREDENTIALS = credentials('dockerhub-cred')  // ID en Jenkins
        IMAGE_NAME = "tebancito/ims_cc"
        BUILD_VERSION = "1.0.${BUILD_NUMBER}"
    }

    stages {

        stage('Checkout') {
            steps {
                echo "=== Clonando repositorio ==="
                git branch: 'main', url: 'https://github.com/dogor123/IMS_CC'
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

        stage('Login to DockerHub') {
            steps {
                echo "=== Iniciando sesión en Docker Hub ==="
                sh """
                    echo ${DOCKERHUB_CREDENTIALS_PSW} | docker login \
                        -u ${DOCKERHUB_CREDENTIALS_USR} --password-stdin
                """
            }
        }

        stage('Test Container') {
            steps {
                echo "=== Probando la imagen antes de subirla ==="
                sh """
                    # Ejecutar contenedor temporal
                    docker run -d --name ims_test -p 8090:80 ${IMAGE_NAME}:${BUILD_VERSION}

                    # Esperar a que Apache/PHP levanten
                    sleep 10

                    echo "=== Ejecutando test HTTP ==="
                    curl -f http://localhost:8090 || (echo "❌ La aplicación no respondió correctamente" && exit 1)

                    echo "=== Test exitoso ==="
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

        stage('Push to DockerHub') {
            steps {
                echo "=== Subiendo imagen a Docker Hub ==="
                sh """
                    docker push ${IMAGE_NAME}:${BUILD_VERSION}
                    docker tag ${IMAGE_NAME}:${BUILD_VERSION} ${IMAGE_NAME}:latest
                    docker push ${IMAGE_NAME}:latest
                """
            }
        }
    }

    post {
        always {
            echo "=== Limpieza final de Docker ==="
            sh 'docker system prune -f || true'
        }
        success {
            echo "✅ Pipeline completado con éxito"
        }
        failure {
            echo "❌ Pipeline falló"
        }
    }
}
