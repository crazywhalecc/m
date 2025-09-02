<?php

use function Laravel\Prompts\confirm;
use function Laravel\Prompts\select;
use function Laravel\Prompts\table;

require "vendor/autoload.php";

const VERSION = '0.1.1';

$config_paths = [
    getenv('HOME') . '/.config/m.ini',
    __DIR__ . '/m.ini',
];

const IGNORE_FILES = ['.DS_Store', 'Thumbs.db'];

// load config
foreach ($config_paths as $path) {
    if (file_exists($path)) {
        $config = parse_ini_file($path);
        break;
    }
}
if (!isset($config)) {
    echo "\e[33mConfig file not found, please contact the developer.\e[0m\n";
    exit(1);
}

// standardize config
$config['search_paths'] = array_filter(array_map('trim', explode(':', $config['search_paths'])));
$config['time_limit'] = intval($config['time_limit']);

// search every paths
$selected_files = [];
foreach ($config['search_paths'] as $search_path) {
    // echo "\e[32mSearching in: $search_path\e[0m\n";
    if (!is_dir($search_path)) {
        continue;
    }
    $files = scandir($search_path);
    // remove . and ..
    $files = array_diff($files, ['.', '..']);
    foreach ($files as $file) {
        if (in_array($file, IGNORE_FILES)) {
            continue;
        }
        // filter only {time_limit} minutes ago files
        if (filemtime($search_path . '/' . $file) >= time() - $config['time_limit'] * 60) {
            $selected_files[] = $search_path . '/' . $file;
        }
    }
    // sort by modified time desc
    usort($selected_files, function ($a, $b) {
        return filemtime($b) - filemtime($a);
    });
}

// output
switch ($argv[1] ?? null) {
    // m help
    case '--help':
    case '-h':
    case 'help':
        echo "\e[32mm - Quick move files to current folder (v" . VERSION . ")\e[0m\n";
        echo "Usage: {$argv[0]} [command]\n";
        echo "Commands:\n";
        echo "  help          Show this help message\n";
        echo "  list, ls, l   List files found in the last {$config['time_limit']} minutes\n";
        echo "  (default)     Move files found in the last {$config['time_limit']} minutes to current directory\n";
        break;
    // m list
    case 'list':
    case 'ls':
    case 'l':
        if ($selected_files) {
            $table_header = ['File Name', 'Size (bytes)', 'Last Modified'];
            $table_rows = [];
            foreach ($selected_files as $file) {
                $table_rows[] = [
                    is_dir($file) ? "{$file}/" : $file,
                    is_file($file) ? filesize($file) : (is_dir($file) ? 'dir' : 'unknown'),
                    // use XXX minutes ago format
                    intval((time() - filemtime($file)) / 60) . ' minutes ago',
                ];
            }

            table($table_header, $table_rows);
        } else {
            echo "\e[33mNo files found in the last {$config['time_limit']} minutes.\e[0m\n";
        }
        break;
    // m (move to current folder)
    default:
        // if multiple files found, ask for confirmation
        if (count($selected_files) > 1) {
            // use k-v to display
            $selected_ls = [];
            foreach ($selected_files as $v) {
                $selected_ls[$v] = is_dir($v) ? "{$v}/" : $v;
                // add time
                $selected_ls[$v] .= ' (' . intval((time() - filemtime($v)) / 60) . ' minutes ago)';
            }
            $selected_file = select(label: "Multiple files found, which file do you want to move?", options: $selected_ls, default: $selected_files[0]);
        } elseif (count($selected_files) === 1) {
            $selected_file = $selected_files[0];
        } else {
            echo "\e[33mNo files found in the last {$config['time_limit']} minutes.\e[0m\n";
            exit(0);
        }
        // move file to current directory using rename
        // ask when file exists
        if (file_exists(getcwd() . '/' . basename($selected_file))) {
            if (confirm("file " . basename($selected_file) . " already exists in current directory, overwrite?", false, 'Overwrite', 'Cancel')) {
                rename($selected_file, getcwd() . '/' . basename($selected_file));
                echo "\e[32mFile {$selected_file} moved to current directory.\e[0m\n";
            } else {
                echo "\e[33mFile move cancelled.\e[0m\n";
                exit(1);
            }
        } else {
            rename($selected_file, getcwd() . '/' . basename($selected_file));
            echo "\e[32mFile {$selected_file} moved to current directory.\e[0m\n";
        }
        break;
}

