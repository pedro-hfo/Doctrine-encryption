# Doctrine-encryption-test 

This repository serves as an example of how to use encryption with [Doctrine](https://www.doctrine-project.org/) in PHP 8.2 using [ParagonIE Halite](https://github.com/paragonie/halite).


## Requirements

* PHP version 8.2.
* Composer Json.
    * doctrine/orm: \^2.16
    * doctrine/annotations: \^1.13
    * doctrine/cache: \^1.11, 
    * paragonie/halite: \^5, 
    * doctrine/migrations: \^3.6
* This project assumes there is a PostgreSQL database accessible to the project.



## Minimal project setup
* Run composer install.

* For database setup, run these commands in order:
    * Enter the psql command line interface with default superuser: `sudo -u postgres psql`
    * Create your user: `CREATE USER yourdbuser WITH PASSWORD 'yourdbpassword';`
    * Create you database: `CREATE DATABASE yourdbname;`
    * Grant all database privileges to your user; `GRANT ALL PRIVILEGES ON DATABASE yourdbname TO yourdbuser;`
    * Connect to your database: `\c yourdbname`
    * Create your table: `CREATE TABLE products (
    id SERIAL PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    address VARCHAR(255) NOT NULL,
    is_encrypted BOOLEAN);`
    * Grant all table privileges to your user: `GRANT ALL PRIVILEGES ON TABLE products TO yourdbuser`
    * Grant usage and select privileges on the products_id_seq sequence to your user `GRANT USAGE, SELECT ON SEQUENCE products_id_seq TO yourdbuser;`

* Now in the bootstrap.php, change the values in `$conn = array(
    'dbname' => 'yourdbname',
    'user' => 'yourdbuser',
    'password' => 'yourdbpassword',
    'host' => 'localhost',
    'driver' => 'pdo_pgsql',
);` to the values chosen for your database.


## Testing
After the initial setup, you should be able to run the test scripts and migrations to see the process
* Run test_encrypted.php
    * There should be a product with an encrypted address in the table: 1 | My Product | MUIFAJvl... | t
* Run test_unencrypted.php
    * There should be a new product with a plain text address in the table: 2 | My Product | Test address | f
* Run migration ` ./vendor/bin/doctrine-migrations migrate`
    * All products should have encrypted addresses
* For further testing you can run the reverse migration ` ./vendor/bin/doctrine-migrations migrate 0`
    * All products should have unencrypted addresses



## Running migrations
This project uses [Doctrine Migrations](https://www.doctrine-project.org/projects/doctrine-migrations/en/3.6/reference/introduction.html) to manage database versions.

The basic command for running migrations is `./vendor/bin/doctrine-migrations migrate`, this will check the Migrations folder for available migrations, then check those against the doctrine_migration_versions table, to decide if there are still migrations to be done. If there are, it will try to migrate sequentially to the latest version. 

You can also run migrations to a specific version, in the case of this project `./vendor/bin/doctrine-migrations migrate Migrations\\VersionEncrypted` or reverse all migrations and go to the database state before any of them `./vendor/bin/doctrine-migrations migrate 0`

The migration available in this project goes through all the rows and encrypts all unencrypted addresses, updating the address and is_encrypted columns, this is done in the up method of the migration file.

The reverse migration is also implemented, using the down method. In this case it goes through all the rows and unencrypts the address field when it is encrypted. This is run when migrating to a version before this one or to the base DB version before migrations.

If you try to migrate again to the same version, it will just be ignored, but this can be bypassed by altering the doctrine_migration_versions, which holds the current db version. If you truncate this table (or remove rows in projects with more than one migration) you can run the same migration multiple times in a row without needing to run the reverse migration to change the DB version.

**One very important thing to note is that this project uses the is_encrypted field to indicate if the relevant columns on the row are encrypted or not, so it shouldn't be changed outside of the migration or other encryption/decryption processes.**


## Project structure
* **src/Product.php** - Product entity mapped to the database through doctrine.
* **services/ProductService.php** - Service to persist and retrieve products.
* **services/EncryptionService.php** - Service that handles encryption/decryption and doctrine lifecycle callbacks.
* **keyGeneration.php** - Script to be run only once to generate the encryption key used by encryption service.
    * The `KeyFactory::save($encKey, 'test_key.key');` line determines the key filename and directory.
    * There are other [more advanced options](https://github.com/paragonie/halite/blob/master/doc/Basic.md) for encryption, such as encrypting/decrypting with associated data or asymmetric-key encryption, but the code would need to be changed accordingly.
* **migrations.php** - Default migrations configuration and versions directory path.
* **migrations-db.php** - Database connection values for migrations, should be the same as the ones used by the project to connect to the DB.
* **Migrations/VersionEncrypted.php** - Migration for encrypting/decrypting the address on all rows.
* **bootstrap.php** - "Main" script, that loads services, creates the database connection and registers the doctrine lifecycle events listener. 
* **test_encrypted.php** - Test script that inserts a product with encrypted data into the database, receives its id and tries to retrieve that id. If working correctly, it should add a row to the table with encrypted address, retrieve it, and log that product with a plain text address.
* **test_unencrypted.php** - Similar to the other test, but doesn't encrypt data. If working correctly, it should add a row to the table with plain text address, retrieve it, and log that product.


## Project Overview
This project uses Doctrine with the [ParagonIE Halite encryption library](https://github.com/paragonie/halite) to encrypt data in a PostgreSQL database:
* **Database connection**: Uses Doctrine to connect with a PostgreSQL database.
* **Entity management**: Sample entity where the address field is encrypted on the database. The `is_encrypted` field indicates the encryption status of the data.
* **Key Management**: Uses `keyGeneration.php` to generate the encryption key, which should then be stored in a safe location and used in EncryptionService.php.
* **Encryption/Decryption Process**: Happens during Doctrine lifecycle events.
    * prePersist and preUpdate: encrypts address and sets is_encrypted to true.
    * postLoad: decrypts address and sets is_encrypted to false in memory, so that it works correctly if later used to update the row.
* **Migration process**: Uses Doctrine Migrations to manage migrations, encrypting/decrypting existing data in this case.


## Adapting to other contexts
1. Adapt the database setup given earlier to your needs, changing the table as needed.
2. Adapt the Product.php entity to reflect your table.
3. Run keyGeneration.php and store the generated key in a safe location and change it accordingly in the bootstrap.php and VersionEncrypted.php files.
4. Adapt ProductService.php to your needs, creating and retrieving the new entity instead of product.
5. Adapt EncryptionService.php to your new entity, deciding which field(s) should be encrypted.
    * Wherever the $object is checked, as in `if ($object instanceof Product) {` should be changed to check for the new entity created.
    * In handleEncryption/handleDecryption, the address field is being encrypted/decrypted. This should be changed to whatever field(s) in your new entity should be encrypted on the database.
6. Likewise, the VersionEncrypted.php migration should also be changed accordingly.
    * In the up method, for each row being contemplated, the encryption process should be changed to reflect the changes made to EncryptionService.php, so that the migration encrypts the same fields being encrypted when inserting elements to the database.
    * In the same way, the down method should be changed to reverse the changes made in the up method (though this can be ignored if the reverse migration won't be used).
7. **Remember that correct handling of the is_encrypted field is paramount for the functioning of this project, as incorrect handling of it could lead to crashes or double encryption.**
8. Adapt test.php to your new entity to test the process.

