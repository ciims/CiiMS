# Plugins

CiiMS offers several plugins that can be added post-installation to enhance and improve your site. All plugins are available via ```composer``` under the ```ciims-plugins``` namespace on packagist, and are configured through the ```ciims_plugins``` key in ```protected/config/params.php```. This page will be updated as new plugins are added to CiiMS.

## File Uploading

By default CiiMS will upload files to the ```uploads``` folder of your webroot. CiiMS instances that span multiple servers, or run in a multi-site configuration require this folder to be synced across multiple servers to ensure file availability. To alleviate this concern, CiiMS offers multiple plugins to upload files to a third party CDN service.

### AWS S3 Uploader

This plugin can be used to upload files to AWS S3. For more information checkout this plugins [github repository](https://github.com/charlesportwoodii/CiiAWSUploader).

##### Installation

After installing this plugin, you can activate it by running the following composer command.

```
composer require ciims-plugins/awsuploader 1.0.0
```

##### Configuration

To use this plugin, add the following to your ```protected/config/params.php``` file:

```
<?php return array(
    [...]
    'ciims_plugins' => array(
        'upload' => array(
            'class' => 'CiiAWSUploader',
            'options' => array(
                'bucket' => ''
                'AWS_ACCESS_KEY' => '',
                'AWS_SECRET_ACCESS_KEY' => ''
            )
        )
    )
    [...]
);
```

A full list of parameters and options can be found on the plugins [github repository](https://github.com/charlesportwoodii/CiiAWSUploader).

__Notes:__

When uploading files to AWS S3, we advise creating a dedicated IAMS user with the following policy:

```
{
  "Version": "<version>",
  "Statement": [
    {
      "Sid": "Stmt1406407897000",
      "Effect": "Allow",
      "Action": [
        "s3:GetBucketAcl",
        "s3:GetObject",
        "s3:GetObjectAcl",
        "s3:GetObjectVersion",
        "s3:GetObjectVersionAcl",
        "s3:PutObject",
        "s3:PutObjectAcl",
        "s3:PutObjectVersionAcl"
      ],
      "Resource": [
        "arn:aws:s3:::<bucket>"
      ]
    }
  ]
}
```

### Rackspace Cloud Files Uploader

This plugin can be used to upload files to Rackspace CloudFiles, or any supported OpenStack CDN. For more information checkout this plugins [github repository](https://github.com/charlesportwoodii/CiiOpenstackUploader).

##### Installation

After installing this plugin, you can activate it by running the following composer command.

```
composer require ciims-plugins/rackspaceuploader 1.0.0
```

##### Configuration

To use this plugin, add the following to your ```protected/config/params.php``` file:

```
<?php return array(
    [...]
    'ciims_plugins' => array(
        'upload' => array(
            'class' => 'CiiOpenstackUploader',
            'options' => array(
                'useOpenstack' => false,    // Set to true to use a generic opensatck container
                'container' => '',          // The container name
                'username' => '',           // Your Openstack username
                'API_KEY' => '',            // Your Openstack API key
                'region' => '',             // The upload region
                'identity' => '',           // Only applies when using a non Rackspace container
            )
        )
    )
    [...]
);
```

A full list of parameters and options can be found on the plugins [github repository](https://github.com/charlesportwoodii/CiiOpenstackUploader).