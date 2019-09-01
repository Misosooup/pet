1. Run composer install
2. Run  php bin/console doctrine:database:create
3. Run php bin/console doctrine:migrations:migrate
4. Run symfony serve

```
POST  http://127.0.0.1:8000/api/pets
```
```
GET  http://127.0.0.1:8000/api/pets/1
```
```
PUT  http://127.0.0.1:8000/api/pets/1
```

Example of post request
```
{
    "category": 1,
    "name": "doggie2",
    "photoUrls": [],
    "tags": ["test", "t1"],
    "status": "pending"
}
```

My approach to microservice architechture.

1. Create another 2 service endpoint for store and user with similar approach
2. Set an api gateway infront of them
3. Each service is deployed into it's own instance receiving traffic from api gateway
4. Each service would have it's own storage mechanism.
5. Communication between services can be through api gateway or internal.

Note: I have only done the pet endpoint and include a test on PetManager.
