This PHP script has a vulnerability related to **Insecure Direct Object References (IDOR)** in the password recovery functionality. The vulnerability occurs because the reset hash mechanism is not well-secured, allowing attackers to manipulate the reset hash or email to reset passwords without proper authorization.

### Vulnerability Explanation:
1. The function `resetHash` generates a new reset hash for a given email and stores it in the database. If the reset is successful, the hash is sent to the user's email address.
2. The function `ChangePasswd` allows users to change their password if they provide a valid email and the correct reset hash.
3. The core issue is that the hash can be manipulated in the URL parameters (`GET` method), and there’s no protection mechanism to ensure that the reset hash was generated and used by the legitimate user.

### Exploit Steps:

1. **Analyze the Code**:
    - The password reset mechanism uses an email and a hash to authenticate the reset process.
    - An attacker can guess or intercept the reset hash sent via email, and since the hash is only 4 digits long (`md5` of a 4-digit integer), it can be brute-forced easily.

2. **Exploit Idea**:
    - The attacker can brute-force the 4-digit reset hash and use it along with the target's email to reset the password.
    - If an attacker can intercept or predict the hash value, they can use it to reset any user's password by sending the `email` and `hash` in the URL.

3. **Crafting the Exploit**:

    - First, the attacker would send a password reset request using the victim's email address. This action will trigger the application to generate and store a new hash for the victim.

    - The attacker can now brute-force or intercept the reset hash. Since the hash is derived from a 4-digit number (`random_int(1000, 9999)`), the search space is relatively small (10,000 possible hashes).

    - Once the correct hash is found, the attacker can use it in a URL like this:
      ```
      http://example.com/reset.php?email=victim@example.com&hash=<valid_hash>
      ```

    - This will bring the attacker to the password reset form for the victim's account, allowing the attacker to set a new password for the victim.

4. **Brute-Force the Hash**:
    - The hash is generated from a 4-digit integer using `md5`. An attacker can write a simple script to brute-force all possible values of `md5` hashes for numbers between 1000 and 9999.

   Example brute-force script in Python:
   ```python
   import hashlib

   target_hash = "target_md5_hash_here"
   for i in range(1000, 10000):
       guess_hash = hashlib.md5(str(i).encode()).hexdigest()
       if guess_hash == target_hash:
           print(f"Found hash: {i}")
           break
   ```

    - Once the correct hash is found, the attacker can use it in the URL as described above to reset the victim’s password.

5. **Change the Password**:
    - After successfully obtaining the valid reset hash, the attacker can proceed to submit a new password for the victim using the password change functionality:
   ```
   http://example.com/reset.php?email=victim@example.com&hash=<valid_hash>
   ```

    - The attacker can submit a POST request with the new password:
   ```html
   <form action="http://example.com/reset.php" method="POST">
       <input type="password" name="passwd" value="newpassword123">
       <input type="submit" value="Change Password">
   </form>
   ```

    - The password will be changed for the victim, and the reset hash will be reset to `NULL`.

### Mitigation Suggestions:
- **Use a more secure hash generation mechanism**: Instead of a 4-digit number and `md5`, use a more secure, randomly generated hash with a longer length (e.g., 64 characters).
- **Set an expiration time for password reset hashes**: Make sure that reset hashes expire after a short time to reduce the attack window.
- **Implement rate-limiting**: To prevent brute-force attacks, limit the number of incorrect hash attempts.
- **Use a token-based system**: Implement token-based authentication for the password reset process, using cryptographically secure tokens sent via email.

### Summary:
This vulnerability can be exploited by brute-forcing or intercepting the password reset hash and using it to reset a victim's password. An attacker can use a script to brute-force the 4-digit `md5` hash and then submit the correct hash in the password reset form to change the victim's password.

