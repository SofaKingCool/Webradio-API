# Webradio-API
A multi-service orientied search and streaming API for your Multi Theft Auto server

## Getting Started
### Prerequisites
You need a web server like *nginx* or *apache* with PHP. The web server should serve content from the `public` directory and not anywhere else to reduce the attack space to a minimum. Due to the early stages of the project you are forced to use `>= PHP 7` because the code uses e.g. the *null coalesce* operator, which was introduced in PHP 7.

### Installing
Clone the repository to your disk, copy the `config.example.php` file as `config.php` in the *root*-directory and open it in an editor of your choice.

Remove the default key in the `$keys` array and add your own secret key strings to the array. There is no character limit on the keys and  you can use any character, whitespace too. Example:
```php
$keys = [
    "2JXR861JPWJ1CENHF51WA3QF3UK1I1QA", // You shouldn't use this one
    "9LRAP2PIJRCDYTQL5844O2VV2QX3TCQ6", // Neither this one
    // ...
];
```

Finally, you should add your private API keys for `YouTube`, `Soundcloud` and `Jamendo`. Other included services do not require an API key.

**Note:** You MUST configure your webserver to point to the `public` directory of the application.

### Development
You should modify the `$debug_mode` variable in `config.php` to *true* if you want to receive the fine details of the exception whenever your application drops an `Out of Order` error.

### Shortcuts for API registration
* [YouTube](https://developers.google.com/youtube/registering_an_application#Create_API_Keys)
* [Soundcloud](http://soundcloud.com/you/apps)
* [Jamendo](https://devportal.jamendo.com/admin/applications)

## Usage
### Search
`/search.php?query=Bohemian+Rhapsody&service=YouTube&key=<a key from $keys>`  
`/search.php?query=Adele+Hello&service=Soundcloud&key=<a key from $keys>`  
`/search.php?query=Sunshine&service=Jamendo&key=<a key from $keys>`  
`/search.php?query=We+Right+Here&service=MyFreeMP3&key=<a key from $keys>`  
`/search.php?query=Bob+Dylan&service=MP3Library&key=<a key from $keys>`  

### Stream
Streaming will be supported in the future.

## Example
### YouTube
`/search.php?query=Bohemian+Rhapsody&service=YouTube&key=<a key from $keys>`
```JSON
[
  {
    "id": "fJ9rUzIMcZQ",
    "title": "Queen - Bohemian Rhapsody (Official Video)"
  },
  {
    "id": "3p4MZJsexEs",
    "title": "Bohemian Rhapsody by Queen FULL HD"
  },
  {
    "id": "irp8CNj9qBI",
    "title": "Queen - Bohemian Rhapsody"
  },
  ...
]
```

### Soundcloud
`/search.php?query=Adele+Hello&service=Soundcloud&key=<a key from $keys>`
```JSON
[
  {
    "id": 230155983,
    "title": "Hello - Adele",
    "duration": 300
  },
  {
    "id": 229791977,
    "title": "Adele - Hello (Cover by Lianna Joy)",
    "duration": 295
  },
  {
    "id": 230335097,
    "title": "Hello ❀ Adele",
    "duration": 294
  },
  ...
]
```

### Error
This example shows the JSON output if you omit the key query parameter in your request.
```JSON
{
  "error": "Unauthorized",
  "errno": 2
}
```

## Contribute
Contributions are always welcome. Feel free to write an ISSUE or PULL REQUEST.

## License
This project is licensed under the MIT License - see the [LICENSE](LICENSE) file for details.
