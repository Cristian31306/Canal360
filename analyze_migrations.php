<?php

$dir = __DIR__ . '/database/migrations';
$files = glob($dir . '/*.php');

$schema = [];

foreach ($files as $file) {
    $content = file_get_contents($file);
    
    // Buscar Schema::create
    if (preg_match_all('/Schema::create\(\'([^\']+)\',/', $content, $matches)) {
        foreach ($matches[1] as $index => $tableName) {
            if (!isset($schema[$tableName])) {
                $schema[$tableName] = [];
            }
            
            // Extract the closure content for this table
            // We use a simple regex to capture lines inside the closure
            $closureStart = strpos($content, "Schema::create('$tableName',");
            $closureEnd = strpos($content, '});', $closureStart);
            if ($closureStart !== false && $closureEnd !== false) {
                $closureContent = substr($content, $closureStart, $closureEnd - $closureStart);
                
                // Match $table->type('columnName'...
                if (preg_match_all('/\$table->([a-zA-Z0-9_]+)\(\'([^\']+)\'/', $closureContent, $colMatches)) {
                    foreach ($colMatches[1] as $i => $type) {
                        $colName = $colMatches[2][$i];
                        $schema[$tableName][] = "$colName ($type)";
                    }
                }
                
                // Match $table->id(); or similar without column name parameter
                if (preg_match_all('/\$table->([a-zA-Z0-9_]+)\(\);/', $closureContent, $autoColMatches)) {
                    foreach ($autoColMatches[1] as $type) {
                        if (in_array($type, ['id', 'timestamps', 'softDeletes'])) {
                            if ($type === 'id') $schema[$tableName][] = "id (id)";
                            if ($type === 'timestamps') {
                                $schema[$tableName][] = "created_at (timestamp)";
                                $schema[$tableName][] = "updated_at (timestamp)";
                            }
                            if ($type === 'softDeletes') {
                                $schema[$tableName][] = "deleted_at (timestamp)";
                            }
                        }
                    }
                }
            }
        }
    }
}

foreach ($schema as $table => $columns) {
    echo "Tabla: " . $table . "\n";
    echo str_repeat("-", strlen("Tabla: " . $table)) . "\n";
    foreach ($columns as $col) {
        echo "- " . $col . "\n";
    }
    echo "\n";
}
