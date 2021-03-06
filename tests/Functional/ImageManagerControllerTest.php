<?php

namespace Tests\Functional;

use App\Http\Controllers\ImageManagerController;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Tests\TestCase;

class ImageManagerControllerTest extends TestCase
{
    /**
     * Test method of ImageManagerController to get the upload filename of a file.
     */
    public function testGetUploadFilename()
    {
        $file = tempnam(sys_get_temp_dir(), 'upl');
        imagepng(imagecreatetruecolor(10, 10), $file);
        $image = new UploadedFile(
            $file,
            'image.png',
            'image/png',
            null,
            null,
            true
        );

        $fileName = ImageManagerController::getUploadFilename($image);
        $this->assertEquals('image.png', $fileName);
    }

    /**
     * Test upload path.
     */
    public function testUploadPath()
    {
        $path = ImageManagerController::getPathYearMonth();
        $this->assertEquals(
            ImageManagerController::PATH_UPLOADS . '/' . date('Y') . '/' . date('m') . '/',
            $path
        );
    }

}
