# TOB - E-commerce System

A PHP-based e-commerce system similar to Sloma, for managing products, categories, orders, and inventory.

## 🚀 Features

- **Product Management**: Add, edit, and manage products
- **Category Management**: Organize products into categories
- **Order Management**: Process and manage customer orders
- **User Management**: Manage user accounts and permissions
- **Featured Products**: Highlight featured products
- **Dashboard**: Overview of sales and inventory
- **API**: RESTful API for integration

## 📊 Architecture

### Technology Stack
- **Backend**: PHP
- **Database**: MySQL
- **Frontend**: HTML, CSS, JavaScript
- **API**: RESTful API endpoints

### Project Structure
```
tob/
├── index.php                  # Main entry point
├── index.html                 # Landing page
├── dashboard.php              # Admin dashboard
├── login.php                  # Login page
├── logout.php                 # Logout handler
├── db_connect.php             # Database connection
├── api.php                    # API endpoint
├── items.php                  # Product listing
├── item_edit.php              # Edit product
├── add_item.php               # Add new product
├── upload_item.php            # Upload product
├── upload.php                 # File upload handler
├── category_edit.php          # Edit category
├── update_category.php        # Update category
├── arrange_order.php          # Order arrangement
├── update_order.php           # Update order
├── featured.php               # Featured products
├── fetch_items.php            # Fetch items API
├── fetch_item_details.php     # Fetch item details
├── user.php                   # User management
├── style.css                  # Styles
└── img/                       # Images
```

## 🔧 Installation

### Prerequisites

- PHP 7.4+
- MySQL 5.7+
- Apache web server

### Setup

```bash
# Clone the repository
git clone <repository-url>
cd tob

# Configure database
# Edit db_connect.php with your database credentials
$conn = new mysqli('localhost', 'username', 'password', 'database_name');

# Create database tables
# Import SQL schema (if available)
mysql -u username -p database_name < schema.sql

# Set permissions
chmod -R 755 img
chmod -R 755 uploads
```

## 🚀 Usage

### Accessing the System

- **Main Page**: `index.php`
- **Dashboard**: `dashboard.php`
- **Login**: `login.php`
- **Products**: `items.php`
- **Add Product**: `add_item.php`
- **Featured**: `featured.php`

### Key Functions

- **Add Item**: `add_item.php` - Add new product
- **Edit Item**: `item_edit.php` - Edit existing product
- **Upload**: `upload.php` - Upload product images
- **Category Edit**: `category_edit.php` - Manage categories
- **Arrange Order**: `arrange_order.php` - Arrange product order
- **Featured**: `featured.php` - Manage featured products

## 🗄️ Database Schema

### Core Tables

- **users**: User accounts and permissions
- **items**: Product information
- **categories**: Product categories
- **orders**: Customer orders
- **featured_items**: Featured product listings

## ⚙️ Configuration

### Database Configuration

Edit `db_connect.php`:

```php
$conn = new mysqli('localhost', 'username', 'password', 'database_name');
```

## 🔒 Security

- Session-based authentication
- Input validation and sanitization
- SQL injection prevention
- File upload security

## 🚧 Production Deployment

### Deployment Checklist

1. Configure production database
2. Set up SSL/HTTPS
3. Configure error reporting
4. Set proper file permissions
5. Configure backup strategy
6. Set up monitoring

## 📧 Support

For questions or support, please open an issue in the repository.

## 📄 License

This project is developed for e-commerce purposes.

---

**Note**: This is an e-commerce system. Ensure proper database configuration and security measures are in place for production use.
