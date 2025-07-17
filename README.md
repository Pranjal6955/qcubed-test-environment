# QCubed PHP Object Injection Test Environment

This directory contains a test environment for CVE-2020-24914 (QCubed PHP Object Injection vulnerability).

## Vulnerability Details

- **CVE**: CVE-2020-24914
- **Severity**: Critical
- **Description**: QCubed 3.1.1 and all versions contain a PHP object injection caused by unserializing untrusted POST data in profile.php
- **CVSS Score**: 9.8

## Setup Instructions

### Using Docker Compose (Recommended)

1. Navigate to this directory:
   ```bash
   cd qcubed-test-environment
   ```

2. Build and start the vulnerable environment:
   ```bash
   docker-compose up -d
   ```

3. The vulnerable application will be available at:
   - URL: http://localhost:8080
   - Profile endpoint: http://localhost:8080/profile.php

### Manual Docker Build

1. Build the Docker image:
   ```bash
   docker build -t qcubed-vulnerable .
   ```

2. Run the container:
   ```bash
   docker run -d -p 8080:80 --name qcubed-test qcubed-vulnerable
   ```

## Testing the Vulnerability

### Manual Testing

1. Send a POST request to the profile.php endpoint:
   ```bash
   curl -X POST http://localhost:8080/profile.php \
     -H "Content-Type: application/x-www-form-urlencoded" \
     -d "data=O:8:\"stdClass\":1:{s:4:\"test\";s:10:\"test123456\";}&action=save&id=1"
   ```

2. Expected response should contain the deserialized object:
   ```
   Deserialized object: object(stdClass)#1 (1) {
     ["test"]=>
     string(10) "test123456"
   }
   ```

### Using Nuclei

1. Run the Nuclei template against the test environment:
   ```bash
   nuclei -u http://localhost:8080 -t qcubed-php-object-injection.yaml -v
   ```

2. For debugging, use the debug flag:
   ```bash
   nuclei -u http://localhost:8080 -t qcubed-php-object-injection.yaml -debug
   ```

## Vulnerability Explanation

The vulnerability occurs in the `profile.php` file where untrusted POST data is passed to PHP's `unserialize()` function. This allows attackers to inject arbitrary PHP objects, which can lead to:

1. **Remote Code Execution (RCE)**: Through gadget chains
2. **Information Disclosure**: Accessing sensitive data
3. **Privilege Escalation**: Manipulating application state

## References

- [CVE-2020-24914](https://cve.mitre.org/cgi-bin/cvename.cgi?name=CVE-2020-24914)
- [Full Disclosure](http://seclists.org/fulldisclosure/2021/Mar/28)
- [Technical Analysis](https://tech.feedyourhead.at/content/QCubed-PHP-Object-Injection-CVE-2020-24914)
- [Security Advisory](https://www.ait.ac.at/themen/cyber-security/pentesting/security-advisories/ait-sa-20210215-01)

## Cleanup

To stop and remove the test environment:

```bash
docker-compose down -v
```

Or for manual Docker:

```bash
docker stop qcubed-test
docker rm qcubed-test
docker rmi qcubed-vulnerable
``` 