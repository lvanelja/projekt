<?php

$now = date('Y-m-d H:i:s');

// Drop tables
$app->db->dropTableIfExists('user')->execute();
$app->db->dropTableIfExists('post')->execute();
$app->db->dropTableIfExists('comment')->execute();
$app->db->dropTableIfExists('tag')->execute();
$app->db->dropTableIfExists('vote')->execute();
$app->db->dropTableIfExists('post_tag')->execute();
$app->db->dropTableIfExists('post_vote')->execute();

$app->db->createTable(
    'user',
    [
        'id' => ['integer', 'primary key', 'not null', 'auto_increment'],
        'email' => ['varchar(80)', 'not null', 'unique'],
        'name' => ['varchar(80)', 'not null'],
        'profile' => ['text'],
        'password' => ['varchar(255)', 'not null'],
        'created' => ['datetime', 'not null'],
    ]
)->execute();

$app->db->createTable(
    'post',
    [
        'id' => ['integer', 'primary key', 'not null', 'auto_increment'],
        'user_id' => ['integer', 'not null'],
        'title' => ['varchar(80)'],
        'body' => ['text', 'not null'],
        'parent' => ['integer'],
        'created' => ['datetime', 'not null'],
        'modified' => ['datetime'],
    ]
)->execute();

$app->db->createTable(
    'comment',
    [
        'id' => ['integer', 'primary key', 'not null', 'auto_increment'],
        'user_id' => ['integer', 'not null'],
        'post_id' => ['integer', 'not null'],
        'body' => ['text', 'not null'],
        'created' => ['datetime', 'not null'],
        'modified' => ['datetime'],
    ]
)->execute();

$app->db->createTable(
    'tag',
    [
        'id' => ['integer', 'primary key', 'not null', 'auto_increment'],
        'name' => ['varchar(80)', 'not null', 'unique'],
    ]
)->execute();

$app->db->createTable(
    'post_tag',
    [
        'id' => ['integer', 'primary key', 'not null', 'auto_increment'],
        'post_id' => ['integer', 'not null'],
        'tag_id' => ['integer', 'not null'],
    ]
)->execute();

$app->db->createTable(
    'vote',
    [
        'id' => ['integer', 'primary key', 'not null', 'auto_increment'],
        'user_id' => ['integer', 'not null'],
        'post_id' => ['integer', 'not null'],
        'vote' => ['integer', 'not null'],
        'created' => ['datetime', 'not null'],
    ]
)->execute();

$app->db->insert(
    'user',
    ['email', 'name', 'password', 'created']
);

$app->db->execute([
    'admin@admin.com',
    'Admin',
    password_hash('admin', PASSWORD_DEFAULT),
    $now
]);