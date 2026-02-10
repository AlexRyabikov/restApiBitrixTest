<?php

use Bitrix\Main\Loader;

Loader::registerAutoLoadClasses('alex.notes', [
    'Alex\\Notes\\NoteRepository' => 'lib/NoteRepository.php',
    'Alex\\Notes\\NoteController' => 'lib/NoteController.php',
]);
