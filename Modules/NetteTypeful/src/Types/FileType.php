<?php declare(strict_types=1);

namespace SeStep\NetteTypeful\Types;

use League\Flysystem\Filesystem;
use Nette\Http\FileUpload;
use SeStep\Typeful\Types\PropertyType;
use SeStep\Typeful\Validation\ValidationError;

class FileType implements PropertyType
{
    const FILE_NOT_FOUND = 'typeful.error.fileNotFound';
    const FILE_UPLOAD_ERROR = 'typeful.error.fileUploadError';

    public function renderValue($value, array $options = [])
    {
        // todo: using file storage in options, and presenter use case, add possibility to download files
        return $value;
    }

    public function validateValue($value, array $options = []): ?ValidationError
    {
        if (!$value) {
            return null;
        }

        if (is_string($value)) {
            /** @var Filesystem $storage */
            $storage = $options['storage'];
            if (!$storage->fileExists($value)) {
                return new ValidationError(self::FILE_NOT_FOUND);
            }

            return null;
        }

        if (!$value instanceof FileUpload) {
            return new ValidationError(ValidationError::INVALID_TYPE);
        }

        if (!$value->hasFile() && $options['nullable'] === true) {
            return null;
        }

        if (!$value->isOk()) {
            return new ValidationError(self::FILE_UPLOAD_ERROR);
        }

        return null;
    }
}
