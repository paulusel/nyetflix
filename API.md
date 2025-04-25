# API CONTRACT

This contract governs the exachange of data between frontend and backend parts of the system.

- Textual data is exchanged in json format
- Authentication method used is JWT sent as `Authorization: BEARER` header
- Except signin/signup api request, all requests are expected to have authorization header
- All responses from backend contain `ok` field. It is a boolean value and indicates success/failure
- All json data fields are required, unless stated otherwise

## Signup

### Endpoint

`/signup.php`

### Request

+ `username` - chosen username of the user
+ `password` - password of the user

Example:

```
{
    "username": "xyzxyz",
    "password": "bazzmoLp"
}
```

### Response

+ `token` - bearer token for use with subsequent request
+ `profile_pic` - profile picture file identifier. Client can issue follow up request with the id to get the photo

Example:
```
{
    "ok": true,
    "token": "ghdExkth485dkjfkdhd9ha0dkhgal8dj18",
    "profile_pic": "dieuiddkhgadkjfdkfjdk"
}
```

## Signin

### Endpoint

`/signin.php`

### Request

+ `username` - username of the user about to login
+ `password` - password of the user

```
{
    "username": "xyzxyz",
    "password": "bazzmoLp"
}
```

### Response

+ `token` - bearer token for use with subsequent request
+ `profile_pic` - profile picture file identifier. Client can issue follow up request with the id to get the photo

Example:
```
{
    "ok": true,
    "token": "ghdExkth485dkjfkdhd9ha0dkhgal8dj18",
    "profile_pic": "dieuiddkhgadkjfdkfjdk"
}
```

## Lists

### Endpoint

`/lists.php`

### Request

Empty

### Response

+ `list` - array of lists

Example

```
{
  "ok": true,
  "list": [
    {
      "list_id": 7476253,
      "list_name": "my_list"
    },
     ...
  ]
}
```

## Series

### Endpoint

`/series.php`

### Request

Empty

### Response

+ list - array of movies whose whose type is series

Example

```
{
  "ok": true,
  "list": [
    {
      "movie_id": 43663684,
      "title": "Breaking bad",
      "description": "A high school chemistry teacher ...",
      "thumbnail": "dkghaidufo8jfh"
    },
    ...
  ]
}
```
