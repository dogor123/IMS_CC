# 📦 IMS_CC – Aplicación Web de Gestión de Inventario
Sistema de gestión de inventario desarrollado en PHP, utilizando Apache y MySQL como servicios base.  
Este repositorio contiene exclusivamente **la aplicación web**, que forma parte de un ecosistema basado en contenedores Docker.
El proyecto incluye integración CI/CD con Jenkins y despliegue automatizado a través de Docker Hub.

---
## 🚀 Características Principales
- Interfaz web construida con PHP + HTML + JS
- Autenticación de usuarios
- Gestión de productos e inventario
- Registro de movimientos (entradas y salidas)
- Arquitectura modernizada mediante Docker
- Integración continua (CI) con Jenkins
- Imagen oficial en Docker Hub:  
  👉 `tebancito/ims_cc`

---
## 🏗 Arquitectura del Proyecto
IMS_CC forma parte de un sistema completo compuesto por tres repositorios:
| Repositorio | Descripción |
|----------------|----------------|
| **IMS_CC** | Aplicación web PHP (este repositorio) |
| **IMS_CC_SQL** | Imagen Docker MySQL con la base de datos preinstalada |
| **IMS_CC_DEPLOY** | Archivo docker-compose.yml para desplegar todo el sistema |

El objetivo es separar responsabilidades:
- *IMS_CC* → Código web  
- *IMS_CC_SQL* → Base de datos lista para usar  
- *IMS_CC_DEPLOY* → Orquestación con docker-compose  
---
## 🐳 Imagen Docker
La imagen de esta aplicación se genera automáticamente mediante un pipeline de Jenkins y se publica en:
👉 **https://hub.docker.com/r/tebancito/ims_cc**

### Ejecutar solo este contenedor (sin base de datos):

docker pull tebancito/ims_cc:latest
docker run -p 8090:80 tebancito/ims_cc:latest

⚠ Nota:
Este modo funciona únicamente si la base de datos ya está disponible en otro contenedor o servidor.

🛠 Requisitos del Sistema
•	Docker o Docker Desktop
•	Navegador moderno (Chrome, Edge, Firefox)
•	Para ambiente de desarrollo se requiere un contenedor MySQL o servicio equivalente


📁 Estructura del Repositorio
IMS_CC/
│
### 🗂️ Descripción de directorios y archivos

├──assets/		** – Recursos estáticos (CSS, JS, imágenes, fuentes).
├──config/		** – Archivos de configuración del proyecto.
├──controllers/	** – Controladores de la aplicación (lógica del negocio).
├──ims/			** – (Puedes decir para qué sirve si me dices).
├──models/		** – Modelos que manejan la lógica de datos.
├──views/		** – Vistas o plantillas presentadas al usuario.
├──docker-compose.yml** – Configuración de servicios Docker.
├──Dockerfile		** – Construcción de la imagen del proyecto.
├──index.php		** – Entrada principal de la aplicación.
├──Jenkinsfile		** – Pipeline CI/CD.
├──login.php / 	** – Manejo de autenticación.
├──logout.php		** – Manejo de autenticación.
├──README.md		** – Documentación del proyecto.

⚙ Configuración de la Conexión a Base de Datos
La aplicación utiliza variables de entorno para conectarse al servicio MySQL:

DB_HOST=db
DB_USER=root
DB_PASS=root
DB_NAME=inventario_db

Estas variables se establecen desde el archivo docker-compose.yml ubicado en:
👉 https://github.com/dogor123/IMS_CC_DEPLOY

🔄 Integración Continua (CI/CD)
Este proyecto utiliza Jenkins para:
•	Lint del Dockerfile (Hadolint)
•	Scan de seguridad (Trivy)
•	Construcción automática de la imagen
•	Pruebas del contenedor
•	Versionado automático
•	Publicación en Docker Hub
Jenkinsfile incluido:
•	main branch → Build + Test + Push
•	Etiquetas:
o	latest
o	1.0.<BUILD_NUMBER>
o	prod-YYYYMMDD

🚀 Despliegue Completo del Sistema
El despliegue del sistema (web + MySQL) se realiza desde:
👉 https://github.com/dogor123/IMS_CC_DEPLOY

Allí se encuentra el archivo:
docker-compose.yml

El despliegue completo se hace así:
git clone https://github.com/dogor123/IMS_CC_DEPLOY
cd IMS_CC_DEPLOY
docker compose pull
docker compose up -d

🧩 Cómo Contribuir
1.	Realizar un fork del repositorio
2.	Crear una rama para la nueva funcionalidad
3.	Hacer commits limpios y documentados
4.	Abrir un Pull Request describiendo los cambios

🐛 Reporte de Problemas
Si encuentras errores, problemas de conexión o bugs:
1.	Abrir un Issue en GitHub
2.	Adjuntar logs del contenedor (si aplica)
3.	Describir cómo reproducir el error

👨‍💻 Autor
Esteban Diaz Vargas
Ingeniería de Informática
Este proyecto forma parte de la asignatura Cloud Computing.

📄 Licencia
Este proyecto es de libre uso académico.
Puedes modificarlo y adaptarlo con fines educativos.
