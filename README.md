# TRack

**TRack** is a lightweight inventory management system built in PHP. It helps organize objects stored on **shelves**, which in turn belong to **racks** — reflecting the structure of real-world storage systems.

---

## Features

- Hierarchical structure: Racks → Shelves → Objects
- Image upload support for objects
- Automatic database setup on first run
- Clean and simple PHP implementation

---

## Installation

Clone the GitHub repository using Git or download it manually:

```bash
git clone https://github.com/LaurinSeeholzer/TRack.git
```

TRack is a web-based PHP application developed for PHP 8 (other versions may work too).
Install PHP using your terminal or from the [official PHP website](https://:php.net/downloads).

```
# For Fedora-based systems
sudo dnf install php php-mysql

# For Debian/Ubuntu systems
sudo apt-get install php php-mysql
```

TRack uses a MySQL database to store its data. Install MySQL via terminal or download it from the [MySQL website](https://dev.mysql.com/downloads):

```
# For Fedora-based systems
sudo dnf install mysqld

# For Debian/Ubuntu systems
sudo apt-get install mysql-server
```
Edit your MySQL connection settings in ```db.php```:

```
$host = 'hostname';
$db   = 'TRack_db';
$user = 'user';
$pass = 'password';
```
Make sure the defined user has access to create databases.
**TRack will automatically create the database and tables if they do not exist.**

Start MySQL (if it isn't running already) and serve the app locally with the PHP development server:

```
# Start MySQL
sudo systemctl start mysqld

# Serve TRack locally (for development)
php -S localhost:8080
```

> ⚠️ The built-in PHP server is intended for development use only.
> For production, use a proper web server such as Apache or Nginx.

##Usage

TRack uses a tree-like structure to organize objects, mirroring how they're physically stored.
The hierarchy:

![TRackUsage](https://github.com/user-attachments/assets/b682ada9-7325-4318-8ec8-2d02dd726016)

A **rack** has the following Attributes:
```
id (int)
name (string)
```

A **shelf** has the following Attributes:
```
id (int)
number (int)
rack_id (int)
```

An **object** has the following Attributes:
```
id (int)
name (string)
object_number (string)
description (string)
defects (string)
quantity (int)
shelf_id (int)
```

You can also upload an image for each object. Uploaded images are stored under ```/upload/{id}.jpg```

**Important Notes**
- Deleting a shelf deletes all its objects.
- Deleting a rack deletes all its shelves and objects.

##Screenshots

##Feedback
Got feedback or suggestions?
Reach out via [laurinseeholzer.ch](https://laurinseeholzer.ch)

##License
[![MIT License](https://img.shields.io/badge/License-MIT-green.svg)](https://choosealicense.com/licenses/mit/)
