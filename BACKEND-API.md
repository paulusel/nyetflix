# BACKEND API CONTRACT

This contract governs the exachange of data between frontend and backend parts of the system.

- Only POST requests are processed. Others are ignored either silently or with message.
- Textual data is exchanged in json format.
- Authentication method used is JWT sent as `Authorization: Bearer ` header.
- Except signin/signup api request, all requests are expected to have authorization header.
- All json data fields are required, unless stated otherwise.
- All responses from backend contain `ok` field. It is a boolean value and indicates success/failure.
- If `ok` field is `false`, it means the request didn't succeed. In that case there will be an accompanying `message` field containing detail.

CAUTION: Exact address of endpoint may differ depending on server document root when deployed.

## Signup

### Endpoint

`api/signup.php`

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

+ `token` - bearer token for use with subsequent requests.
+ `user` -  user data. `picture` field of user data optional.

Example:
```
{
    "ok": true,
    "token": "ghdExkth485dkjfkdhd9ha0dkhgal8dj18",
    "user": {
        "role": "user",
        "username": "user",
        "picture": "dieuiddkhgadkjfdkfjdk"
    }
}
```

## Signin

Creates new account.

### Endpoint

`api/signin.php`

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
+ `user` -  user data. `picture` field of user data optional.

Example:
```
{
    "ok": true,
    "token": "ghdExkth485dkjfkdhd9ha0dkhgal8dj18",
    "user": {
        "role": "user",
        "username": "user",
        "picture": "dieuiddkhgadkjfdkfjdk"
    }
}
```

## Lists

Fetches playlists of a user.

### Endpoint

`api/lists.php`

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

Fetches on tv-series types of movies and not short films.

### Endpoint

`api/series.php`

### Request

Empty

### Response

+ `list` - array of movies whose whose type is series

Example

```
{
  "ok": true,
  "list": [
    {
      "movie_id": 43663684,
      "title": "Breaking Bad",
      "description": "A high school chemistry teacher ...",
      "thumbnail": "dkghaidufo8jfh"
    },
    ...
  ]
}
```

## Username

Checks if a username is available or already taken. This can be used to check availability of username during signup.

### Endpoint

`api/username.php`

### Request

Single string and only string specifying username being requested.

Example: ```"myusername"```

### Response

+ `available` - boolean value. `true` means username is not taken and `false` otherwise.

Example:

```
{
    "ok": true,
    "available": true
}
```

## Movie

Fetch movie information for a specific movie.

### Endpoint

`api/movie.php`

### Request

A single and only string specifying movie id whose information is being requested.

Example: ```"amovieidkdhfk"```

### Response

+ `movie`: movie information. If json has video section, it is means the movie is a short film. Otherwise it is a series.

Example:

```
  "ok": true,
  "movie": {
      "movie_id": 43663684,
      "title": "Breaking Bad",
      "description": "A high school chemistry teacher ...",
      "thumbnail": "dkghaidufo8jfh",
      "video":  "dkdkhgadkkdjfk"
    }
```

## Seasons

For movies that series type, fetches all seasons of the series and their episodes.

### Endpoint

`api/seasons.php`

### Request

A single and only string specifying movie series id whose seasons is being requested.

Example: ```"aseriesmovidadid"```

### Response

+ `movie`: movie information. If json has video section, it is means the movie is a short film. Otherwise it is a series.

Example:

```
  "ok": true,
  "movie_id": 843940098,
  "seasons": [
    {
        "season_id": 74593873,
        "season_no": 2,
        "title": "The desert",
        "description": "Two guys wandering out in the desert ...",
        "episodes": [
            {
                "episode_no": 1,
                "movie_id": 43663684,
                "title": "Breaking Bad",
                "description": "A high school chemistry teacher ...",
                "thumbnail": "dkghaidufo8jfh",
                "video":  "dkdkhgadkkdjfk"
            },
            {
                ...
            }
        ]
    },
    {
        ...
    }
]
```

## Updateme

Update user information. This can be used to change user password and the like.

### Endpoint

`api/updateme.php`

### Request

```
{
    "password": "bazzmorp"
}
```

### Response

Just `ok` field which is found in every response by default. Its value indicates success/failure of update operation.

Example:

```
{
    "ok": true
}
```

## Deleteme

Delete user account.

### Endpoint

`api/delete.php`

### Request

Empty. User account to be deleted is determined from authentication token.

### Response

Just `ok` field which is found in every response by default. Its value indicates success/failure of delete operation.

Example:

```
{
    "ok": true
}
```

## Watch

Start streaming a video. Can be used to fetch both playlists and segments.

### Endpoint

`api/watch.php`

### Request

+ `movie_id` - Id of movie whose stream data is being requested.
+ `request_type` - type information requested. Either `manifest` for playlist or `segement` for fetching segments.
+ `segment_info` - (optional) if request type is `segment`, this array provides more info about the segment. It has:
    - `sgement_no` - if request type is `segment`, segment_no of the segment.
    - `start` - starting position of segment(in seconds) from the beginning video.

Example: playlist request
```

{
    "movie_id": 4808477627,
    "request_type": "manifest"
}
```
Example: segment request

```
{
    "movie_id": 4808477627,
    "request_type": "segment",
    "segment_info" : {
        "segment_no": 353,
        "start": 23454
    }
}
```

### Response

Binary data. `Content-Type` header is set to `application/vnd.apple.mpegurl` for playlist response and `video/MP2T` to segment responses.

