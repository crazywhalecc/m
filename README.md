# m

Easily move recent downloaded files to the current folder.

## Features

- Move the most recently downloaded file from your default download directory to your current working directory.
- Supports common operating systems: Linux and macOS.
- Simple command-line interface.
- Lightweight and fast.
- No dependencies required, written by PHP, packed by [box](https://box-project.github.io/), distributed by [static-php-cli](https://static-php.dev).
- Beautiful console output using [laravel/prompts](https://laravel.com/docs/12.x/prompts).

## Usage

First, download the `m` executable from the [releases page](https://github.com/crazywhalecc/m/releases) and place it in a directory included in your system's PATH.

Then, you can use the `m` command followed by the filename to move the file from your default download directory to your current working directory.

Download and extract the appropriate binary for your system:

```bash
# Linux x86_64
curl -fsSL https://github.com/crazywhalecc/m/releases/latest/download/m-linux-x86_64.tgz -o m-linux-x86_64.tgz && tar -zxvf m-linux-x86_64.tgz && rm m-linux-x86_64.tgz
# macOS x86_64 (Intel)
curl -fsSL https://github.com/crazywhalecc/m/releases/latest/download/m-macos-x86_64.tgz -o m-macos-x86_64.tgz && tar -zxvf m-macos-x86_64.tgz && rm m-macos-x86_64.tgz
# linux aarch64
curl -fsSL https://github.com/crazywhalecc/m/releases/latest/download/m-linux-aarch64.tgz -o m-linux-aarch64.tgz && tar -zxvf m-linux-aarch64.tgz && rm m-linux-aarch64.tgz
# macOS aarch64 (Apple Silicon)
curl -fsSL https://github.com/crazywhalecc/m/releases/latest/download/m-macos-aarch64.tgz -o m-macos-aarch64.tgz && tar -zxvf m-macos-aarch64.tgz && rm m-macos-aarch64.tgz
```

Move `m` to a directory in your PATH, for example:

```bash
mv m /usr/local/bin/
```

Now you can use the `m` command in your terminal:

```bash
# Help
m --help
# Move the most recent downloaded file to the current directory
m
```

Or, you could preview the file list:

```bash
m list
```
