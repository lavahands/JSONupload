# JSONupload

Website to upload files which are checked for following rules:
- File's content is in JSON format
- JSON content includes the following keys: name, phone, address, website. Must exist and filled
- Phone number can only use numbers (0-9), hyphen (-), space or parenthesis ()
- Website must be syntactically correct ie. domain.tld (both https://www.scr.ee/minuveeb and scr.ee are allowed)

File can contain one or more JSON objects.
After validation gives a report for each object 

Needs php_fileinfo.dll/php_fileinfo.so enabled at php.ini
