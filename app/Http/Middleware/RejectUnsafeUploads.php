<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Symfony\Component\HttpFoundation\Response;

/**
 * Stops executable or scriptable files reaching public/uploads.
 *
 * Controllers build the stored file name from the client's file name or from
 * the extension implied by the client's Content-Type, and several of those
 * routes need no login, so every upload is checked here before routing.
 */
class RejectUnsafeUploads
{
    /**
     * The file types the application's upload forms accept.
     */
    private const ALLOWED_EXTENSIONS = [
        'jpg', 'jpeg', 'jpe', 'png', 'gif', 'webp', 'bmp', 'svg', 'heic', 'heif',
        'mp4', 'm4v', 'avi', 'mov', 'qt', '3gp', '3gpp', 'ogg', 'ogv', 'ogx', 'webm', 'mkv', 'wmv', 'mpg', 'mpeg',
        'pdf',
    ];

    /**
     * Extensions a web server may execute even when they are not the last one (a.php.jpg).
     */
    private const EXECUTABLE_SEGMENT = '/^(php\d*|phtml|pht|phar|phps|cgi|pl|py|asp|aspx|jsp|sh|shtml)$/i';

    public function handle(Request $request, Closure $next): Response
    {
        foreach ($this->uploadedFiles($request->allFiles()) as $file) {
            if (! $this->isSafe($file)) {
                return $this->reject($request);
            }
        }

        return $next($request);
    }

    /**
     * @return iterable<UploadedFile>
     */
    private function uploadedFiles(array $files): iterable
    {
        foreach ($files as $file) {
            if (is_array($file)) {
                yield from $this->uploadedFiles($file);
            } elseif ($file instanceof UploadedFile) {
                yield $file;
            }
        }
    }

    private function isSafe(UploadedFile $file): bool
    {
        // Windows ignores trailing dots and spaces, so "a.php." would be stored as "a.php".
        $name = rtrim($file->getClientOriginalName(), ". \t\r\n\0");
        $segments = explode('.', $name);
        array_shift($segments);

        foreach ($segments as $segment) {
            if (preg_match(self::EXECUTABLE_SEGMENT, $segment)) {
                return false;
            }
        }

        // No extension, or a generic binary type, cannot produce an executable file name.
        return $this->isAllowedExtension(pathinfo($name, PATHINFO_EXTENSION))
            && $this->isAllowedExtension($file->clientExtension());
    }

    private function isAllowedExtension(string $extension): bool
    {
        return $extension === ''
            || strtolower($extension) === 'bin'
            || in_array(strtolower($extension), self::ALLOWED_EXTENSIONS, true);
    }

    private function reject(Request $request): Response
    {
        $message = 'This file type is not allowed.';

        // The mobile API reports validation failures as HTTP 200 with ResponseCode 2.
        if ($request->is('api/*')) {
            return response()->json(['ResponseCode' => '2', 'ResponseText' => $message]);
        }

        if ($request->ajax() || $request->expectsJson()) {
            return response()->json(['msg' => $message, 'type' => 'error'], 422);
        }

        abort(422, $message);
    }
}
