<?php

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Route;

beforeEach(function () {
    Route::post('/_upload-test', fn () => response()->json(['stored' => true]));
    Route::post('/api/_upload-test', fn () => response()->json(['stored' => true]));
});

function uploadOf(string $name, string $mime): UploadedFile
{
    // UploadedFile::fake() ignores the reported type here, and the type is what is under test.
    $path = tempnam(sys_get_temp_dir(), "upload");
    file_put_contents($path, "content");

    return new UploadedFile($path, $name, $mime, null, true);
}

it('accepts the file types the upload forms use', function (string $name, string $mime) {
    $this->post('/_upload-test', ['file' => uploadOf($name, $mime)])
        ->assertOk()
        ->assertJson(['stored' => true]);
})->with([
    ['photo.jpg', 'image/jpeg'],
    ['PHOTO.PNG', 'image/png'],
    ['logo.svg', 'image/svg+xml'],
    ['clip.mov', 'video/quicktime'],
    ['clip.mp4', 'video/mp4'],
    ['clip.ogg', 'video/ogg'],
    ['edition.pdf', 'application/pdf'],
    ['avatar.jpg', 'application/octet-stream'],
    ['blob', 'application/octet-stream'],
]);

it('rejects executable or scriptable uploads', function (string $name, string $mime) {
    $this->withHeaders(['Accept' => 'application/json'])
        ->post('/_upload-test', ['file' => uploadOf($name, $mime)])
        ->assertStatus(422)
        ->assertJsonMissing(['stored' => true]);
})->with([
    'php file' => ['shell.php', 'image/jpeg'],
    'upper case' => ['shell.PhP', 'image/jpeg'],
    'phtml' => ['shell.phtml', 'image/jpeg'],
    'php7' => ['shell.php7', 'image/jpeg'],
    'double extension' => ['shell.php.jpg', 'image/jpeg'],
    'windows trailing dot' => ['shell.php.', 'image/jpeg'],
    'windows stream' => ['shell.php::$DATA', 'image/jpeg'],
    'htaccess' => ['.htaccess', 'application/octet-stream'],
    'html' => ['page.html', 'text/html'],
    'php content type with safe name' => ['logo.jpg', 'application/x-httpd-php'],
    'html content type with safe name' => ['logo.jpg', 'text/html'],
]);

it('checks files nested in arrays', function () {
    $this->withHeaders(['Accept' => 'application/json'])
        ->post('/_upload-test', ['allMediaFiles' => [
            uploadOf('photo.jpg', 'image/jpeg'),
            uploadOf('shell.php', 'image/jpeg'),
        ]])
        ->assertStatus(422);
});

it('answers the mobile API in its own response format', function () {
    $this->post('/api/_upload-test', ['file' => uploadOf('shell.php', 'image/jpeg')])
        ->assertOk()
        ->assertJson(['ResponseCode' => '2']);
});
