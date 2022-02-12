<?php

return [

    /*
     |--------------------------------------------------------------------------
     | Larex Settings
     |--------------------------------------------------------------------------
     */

    /**
     * Default CSV settings.
     */
    'csv' => [
        'path' => lang_path('localization.csv'),
    ],

    /**
     * Exporters.
     */
    'exporters' => [
        // Default exporter
        'default' => 'laravel',

        // Available exporters
        'list' => [
            'laravel' => Lukasss93\Larex\Exporters\LaravelExporter::class,
            'json:lang' => Lukasss93\Larex\Exporters\JsonLanguagesExporter::class,
            'json:group' => Lukasss93\Larex\Exporters\JsonGroupsExporter::class,
            'crowdin' => Lukasss93\LarexCrowdin\Exporters\CrowdinExporter::class,
        ],
    ],

    /**
     * Importers.
     */
    'importers' => [
        // Default importer
        'default' => 'laravel',

        // Available importers
        'list' => [
            'laravel' => Lukasss93\Larex\Importers\LaravelImporter::class,
            'json:lang' => Lukasss93\Larex\Importers\JsonLanguagesImporter::class,
            'json:group' => Lukasss93\Larex\Importers\JsonGroupsImporter::class,
            'crowdin' => Lukasss93\LarexCrowdin\Importers\CrowdinImporter::class,
        ],
    ],

    /**
     * Linters to run with larex:lint command.
     */
    'linters' => [
        Lukasss93\Larex\Linters\ValidHeaderLinter::class,
        Lukasss93\Larex\Linters\ValidLanguageCodeLinter::class,
        Lukasss93\Larex\Linters\DuplicateKeyLinter::class,
        Lukasss93\Larex\Linters\ConcurrentKeyLinter::class,
        Lukasss93\Larex\Linters\NoValueLinter::class,
        // Lukasss93\Larex\Linters\DuplicateValueLinter::class,
        Lukasss93\Larex\Linters\UntranslatedStringsLinter::class,
        Lukasss93\Larex\Linters\UnusedStringsLinter::class,
        Lukasss93\Larex\Linters\ValidHtmlValueLinter::class,
    ],

    /**
     * Search criteria for file used by:
     * - UntranslatedStringsLinter
     * - UnusedStringsLinter
     * - LarexLocalizeCommand.
     */
    'search' => [
        /**
         * Directories which should be looked inside.
         * NOTE: It's recursive.
         */
        'dirs' => ['resources/views', 'app'],

        /**
         * Patterns by which files should be queried.
         * The values can be a regular expression, glob, or just a string.
         */
        'patterns' => ['*.php'],

        /**
         * Functions that the strings will be extracted from.
         * Add here any custom defined functions.
         * NOTE: The translation string should always be the first argument.
         */
        'functions' => ['__', 'trans', '@lang'],
    ],

    /**
     * End of line used by:
     * - LaravelExporter.
     */
    'eol' => "\n",
];
