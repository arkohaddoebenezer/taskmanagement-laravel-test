# **Lightweight Task Management Application**

A simple Laravel web application for task management.

## **Features**
- Create tasks
- Edit tasks
- Delete tasks
- Drag and drop to re-order task priority

---

## **Local Setup and Deployment from ZIP File**
**Note:** The provided ZIP file contains a built version of the application and can be used as-is without rebuilding.

To set up this application locally, we highly recommend using **Laravel Herd** because it offers a one-click PHP development environment with zero dependencies.

### **Step 1: Install Herd**
1. Download and install **Herd** from [Herd Official Website](https://herd.laravel.com/).
2. Run the installer and follow the setup instructions.
3. Verify installation by running the following commands in Terminal (macOS) or Command Prompt (Windows):
   ```bash
   php --version
   laravel --version
   composer --version
   node --version
   ```
4. Keep **Herd** running in the background.

### **Step 2: Extract the Project**
1. Download and extract the project ZIP file (**taskmanagement.zip**).
2. Move the extracted project folder to the **Herd projects** directory (typically located under `~/Herd/projects/`).

### **Step 3: Add Project to Herd**
1. Open **Herd**.
2. Click **Add Project** and select the extracted project folder.
3. The application will be served at:
   ```
   http://taskmanagement.test
   ```
4. Open any browser and visit `taskmanagement.test` to start using the application.

---

## **Database Configuration**
### **SQLite (Default Configuration)**
The application is configured with SQLite for easy testing. If you prefer MySQL or another database, follow these steps:
1. Open the `.env` file in the project's root directory.
2. Uncomment and modify the database configuration:
   ```ini
   DB_CONNECTION=mysql
   DB_HOST=127.0.0.1
   DB_PORT=3306
   DB_DATABASE=laravel
   DB_USERNAME=root
   DB_PASSWORD=
   ```
3. Open the terminal in the project’s directory and run:
   ```bash
   herd php artisan migrate
   ```
   This will create the necessary database tables.

---

## **Enjoy The Lightweight Task Management Application!**

