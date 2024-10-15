# Social Media Waves (akira Santhakumaran)

## Project Overview

The **web.triilink.socialmediaProject** is a PHP-based social media platform designed to allow users to connect, share, and engage with content in real time. This project focuses on creating a user-friendly and responsive social media experience using modern web technologies.

## Features

- **User Registration and Authentication**: Secure login and registration system with password encryption.
- **Profile Management**: Users can edit their profiles, update their pictures, and customize their bio.
- **Posting and Interactions**: Users can create posts, like, comment, and share content.
- **Real-Time Notifications**: Updates on new likes, comments, and posts through live notifications.
- **Media Uploads**: Support for image and video uploads with validation.
- **Responsive Design**: Optimized for both desktop and mobile use.

## Tech Stack

- **Backend**: PHP 8.x
- **Frontend**: HTML5, CSS3, JavaScript (or any frameworks you are using like Vue.js, React, etc.)
- **Database**: MySQL (or MariaDB)
- **Other**: [Any additional tools or libraries you're using]

## Installation Guide

1. **Clone the repository**:
   ```bash
   git clone https://github.com/Akira98000/php.socialmedia.wave.git
   ```

2. **Navigate to the project directory**:
   ```bash
   cd php.socialmedia.wave
   ```

3. **Configure your environment**:
   - Set up your `.env` file with your database and other environment-specific configurations (or configure `config.php`).
   - Example `.env`:
     ```bash
     DB_HOST=localhost
     DB_USER=root
     DB_PASS=password
     DB_NAME=socialmedia
     ```

4. **Install dependencies**:
   - Ensure PHP and MySQL are installed on your system.
   - If you're using a dependency manager like Composer, run:
     ```bash
     composer install
     ```

5. **Set up the database**:
   - Import the database schema:
     ```bash
     mysql -u root -p socialmedia < database/socialmedia.sql
     ```

6. **Start the server**:
   ```bash
   php -S localhost:8000
   ```

7. **Access the app**:
   Open your browser and navigate to `http://localhost:8000`.

## Usage

- **Register**: Sign up for a new account.
- **Create a post**: Share your thoughts or media.
- **Interact**: Like, comment, or share posts from others.
- **Customize**: Edit your profile information and manage your account settings.
