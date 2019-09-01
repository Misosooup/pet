1. Run composer install
2. Run  php bin/console doctrine:database:create
3. Run php bin/console doctrine:migrations:migrate
4. Run symfony serve

Add pet
POST  http://127.0.0.1:8000/api/pets

Get Pet
GET  http://127.0.0.1:8000/api/pets/1

Update Pet
PUT  http://127.0.0.1:8000/api/pets/1

Example of post request
{
    "category": 1,
    "name": "doggie2",
    "photoUrls": [],
    "tags": ["test", "t1"],
    "status": "pending"
}