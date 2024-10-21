---
title: "Docs"
description: "Create accessibility reports using pa11y in php."
sidebar: auto
---

## Introduction

Pa11y is an open-source, automated accessibility reporting tool. It's audits provide insights
into accessibility issues existing on a webpage.

This package makes it easy to run Pa11y using PHP. Here is an example of how to get the number of
accessibilty errors, warnings and notices on a page:

```php
// Example goes here
```

It's easy to configure various options:

```php
// TODO
```

Here is how you can get the details of the first error on the page:

## Requirements

The pa11y-php package requires **PHP 8.1+**, and **node 16 or higher**.

## Installation & setup

You can install the package via composer:

```bash
composer require jk-oster/pa11y-php
```

The package relies on the ``pa11y-ci`` and ``chrome-launcher`` js packages being availabe on your system.
In most cases you can accomplish this by running the following commands:

```bash
npm install pa11y-ci
npm install chrome-launcher
```

Chromium should also be available on your server. On modern versions of Ubuntu, you can do that with this command:

```bash
sudo apt install chromium-browser
```
