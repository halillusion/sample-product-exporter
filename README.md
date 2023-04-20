# Sample Product Exporter
## Laravel based product export project.

### Step 1:
Run command ```compose up``` in project directory

### Step 2:
Go to ```localhost:8000```


#### Routes
- [GET] /products - Product list
- [POST] /products/export - Product export (with platform and format parameters)

### Unit Test
Run command ```./vendor/bin/phpunit``` in project directory

### Postman Test
Import content ```postman_collection.json``` file in project directory.