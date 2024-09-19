To exploit the Insecure Direct Object References (IDOR) vulnerability in this Flask application, we can manipulate the `userdata` cookie to escalate privileges and gain access to the admin dashboard. Here is a step-by-step guide to exploiting the vulnerability:

### Steps:

1. **Analyze the Code**:
    - The application reads a `userdata` cookie that is base64 encoded.
    - It decodes this cookie, loads it as JSON, and checks if the `role` is set to `'admin'`.
    - If the `role` is `'admin'`, the user is granted access to the admin dashboard.

2. **Exploit Idea**:
    - Since the application trusts the `userdata` cookie without verifying its integrity (no cryptographic signing or HMAC used), we can modify the cookie value to escalate our privileges to an admin.

3. **Capture and Decode the Cookie**:
    - First, capture the `userdata` cookie using browser developer tools or a proxy like Burp Suite.
    - The cookie might look something like this (base64 encoded):
      ```
      eyJ1c2VybmFtZSI6ICJ1c2VyIiwgInJvbGUiOiAiZ3Vlc3QifQ==
      ```
    - Decode it using a base64 decoder:
      ```bash
      echo "eyJ1c2VybmFtZSI6ICJ1c2VyIiwgInJvbGUiOiAiZ3Vlc3QifQ==" | base64 -d
      ```
      The decoded JSON might look like:
      ```json
      {
        "username": "user",
        "role": "guest"
      }
      ```

4. **Modify the Cookie**:
    - Change the `role` from `"guest"` to `"admin"`.
      ```json
      {
        "username": "user",
        "role": "admin"
      }
      ```
    - Re-encode the modified JSON using base64:
      ```bash
      echo -n '{"username": "user", "role": "admin"}' | base64
      ```
    - This should give you an updated base64 string, such as:
      ```
      eyJ1c2VybmFtZSI6ICJ1c2VyIiwgInJvbGUiOiAiYWRtaW4ifQ==
      ```

5. **Set the Modified Cookie**:
    - Use browser developer tools or a proxy tool to modify the `userdata` cookie in the request.
    - Set the value of `userdata` to the new base64 string (`eyJ1c2VybmFtZSI6ICJ1c2VyIiwgInJvbGUiOiAiYWRtaW4ifQ==`).

6. **Access the Admin Dashboard**:
    - Reload the page. The application will decode the modified cookie, see that the `role` is now `admin`, and grant you access to the admin dashboard.

### Summary:
By manipulating the `userdata` cookie and changing the role to `admin`, we can exploit the IDOR vulnerability and gain unauthorized access to the admin functionality of the application.

Let me know if you need further assistance with this or if you have any other questions!