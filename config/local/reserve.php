<?php

return [

  /*
  |--------------------------------------------------------------------------
  | Output Locations
  |--------------------------------------------------------------------------
  |
  | You can declare the paths you wish to place your reserves here. As the
  | titles indicate, folders are for folder reserve paths and databases for
  | database reserve paths. Error files are used to output any stderr's from
  | the shell.
  |
  */

  'output'    => [

    'folders'   => [

      'path'   => application_path('reserves/folders'),
      'errors' => application_path('reserves/folders/_errors.txt')

    ],

    'databases' => [

      'path'   => application_path('reserves/databases'),
      'errors' => application_path('reserves/databases/_errors.txt')

    ],

  ],


  /*
  |--------------------------------------------------------------------------
  | Folders to Reserve
  |--------------------------------------------------------------------------
  |
  | The key indicates the name you wish to use for the generated reserve,
  | while the values should be paths to the folders you wish to reserve.
  |
  */

  'folders'   => [

    'folder'  => '/path/to/folder',

  ],


  /*
  |--------------------------------------------------------------------------
  | Databases to Reserve
  |--------------------------------------------------------------------------
  |
  | The key indicates the name you wish to use for the generated
  | reserve, while the values should be the names of the databases
  | you wish to reserve.
  |
  */

  'databases' => [

    'database' => 'dummy_database'

  ]

];