This PHP script is vulnerable to an Insecure Direct Object References (IDOR) attack due to a logical flaw in the `if` statement. Specifically, the conditional check uses the `!=` operator incorrectly, which means we can bypass the check and access sensitive information even if we are not authorized.

### Vulnerability Explanation:
The vulnerable part of the code is this:
```php
if ($sess != '4ebdbacd03dc1a3116f62efdd9c58f06df46de3b5d3dce409257ded24f44bb04' && $user != 'tom' ) {
    echo "You are not authorized to view this content.";
    return;
}
```

- The condition checks whether **both** the session token (`$sess`) and the username (`$user`) are not valid.
- This means that if **either** the session token is valid or the username is `tom`, the condition will not block the user, allowing unauthorized access.

### Exploit Steps:

1. **Analyze the Code**:
    - The session token (`usess`) and user (`user`) values are retrieved from cookies.
    - The condition only checks that both the session and user are invalid, but if **one of them** is valid, access is granted.

2. **Exploit Idea**:
    - We can exploit this vulnerability by either:
        - Using a valid session token (`4ebdbacd03dc1a3116f62efdd9c58f06df46de3b5d3dce409257ded24f44bb04`) with any username.
        - Or, using the username `tom` with any session token.

3. **Craft the Exploit**:

   **Approach 1: Use a valid session token**
    - Set the session cookie (`usess`) to the valid session token:
      ```
      usess = 4ebdbacd03dc1a3116f62efdd9c58f06df46de3b5d3dce409257ded24f44bb04
      ```
    - Set any value for the `user` cookie. For example:
      ```
      user = random_user
      ```
    - Set the `id` cookie to the user ID you want to view:
      ```
      id = 1
      ```
    - Now when you request the URL, the condition will pass because the session token is valid, and you'll be able to access the file (e.g., `details/1.json`).

   **Approach 2: Use a valid username (`tom`)**:
    - Set the `user` cookie to `tom`:
      ```
      user = tom
      ```
    - Set any value for the `usess` cookie. For example:
      ```
      usess = random_token
      ```
    - Set the `id` cookie to the user ID you want to view:
      ```
      id = 1
      ```
    - Now when you request the URL, the condition will pass because the username is `tom`, and you'll be able to access the file (e.g., `details/1.json`).

4. **Exploit the Application**:
    - You can modify cookies using browser developer tools or a tool like Burp Suite. Set the cookies as described above and then visit the page (e.g., `http://example.com/?details=1`) to view the content of the specified file.

### Summary:
This vulnerability can be exploited by either:
- Using a valid session token with any username.
- Using the username `tom` with any session token.

By manipulating the session token or username in the cookies, you can bypass the authorization check and access sensitive information from the `details` directory.
