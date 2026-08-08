<?php

namespace Tests\Feature;

use App\Http\Controllers\AdminStudentController;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Auth;
use Tests\TestCase;

class AdminStudentControllerTest extends TestCase
{
    public function test_zip_upload_without_supported_images_throws_runtime_exception(): void
    {
        $controller = new AdminStudentController();
        $zipPath = sys_get_temp_dir() . '/zip_test_' . uniqid() . '.zip';

        $zip = new \ZipArchive();
        $zip->open($zipPath, \ZipArchive::CREATE);
        $zip->addFromString('sample.png', 'not-an-image');
        $zip->close();

        $extractDir = sys_get_temp_dir() . '/zip_extract_' . uniqid();

        if (!is_dir($extractDir)) {
            mkdir($extractDir, 0777, true);
        }

        $method = new \ReflectionMethod(AdminStudentController::class, 'getProfilePictureFilesFromZip');
        $method->setAccessible(true);

        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('The ZIP file must contain JPG or JPEG image files.');

        $method->invoke($controller, $zipPath, $extractDir);
    }

    public function test_zip_upload_with_pdf_documents_returns_success(): void
    {
        $zipPath = sys_get_temp_dir() . '/zip_test_' . uniqid() . '.zip';

        $zip = new \ZipArchive();
        $zip->open($zipPath, \ZipArchive::CREATE);
        $zip->addFromString('AG2018001.pdf', '%PDF-1.4 sample');
        $zip->close();

        $uploadedFile = new UploadedFile($zipPath, 'documents.zip', 'application/zip', null, true);
        $request = Request::create('/admin/student/upload-documents', 'POST', [], [], ['file' => $uploadedFile]);

        $mockUser = \Mockery::mock();
        $mockUser->shouldReceive('hasPermissionTo')->with('student:upload')->andReturn(true);
        $mockUser->shouldReceive('hasRole')->with('Admin')->andReturn(false);
        Auth::shouldReceive('user')->andReturn($mockUser);

        $controller = new AdminStudentController();
        $response = $controller->upload_documents($request);

        $this->assertEquals(200, $response->status());
        $this->assertTrue($response->getData()->success);
    }

    public function test_zip_upload_with_only_non_pdf_documents_returns_422(): void
    {
        $zipPath = sys_get_temp_dir() . '/zip_test_' . uniqid() . '.zip';

        $zip = new \ZipArchive();
        $result = $zip->open($zipPath, \ZipArchive::CREATE);
        if ($result !== true) {
            $this->fail('Failed to create zip archive: ' . $result);
        }
        $zip->addFromString('sample.txt', 'not-a-pdf');
        $zip->addFromString('image.png', 'not-a-pdf');
        $zip->close();

        $uploadedFile = new UploadedFile($zipPath, 'invalid-docs.zip', 'application/zip', null, true);
        $request = Request::create('/admin/student/upload-documents', 'POST', [], [], ['file' => $uploadedFile]);

        $mockUser = \Mockery::mock();
        $mockUser->shouldReceive('hasPermissionTo')->with('student:upload')->andReturn(true);
        $mockUser->shouldReceive('hasRole')->with('Admin')->andReturn(false);
        Auth::shouldReceive('user')->andReturn($mockUser);

        $controller = new AdminStudentController();
        $response = $controller->upload_documents($request);

        $this->assertEquals(422, $response->status());
        $this->assertSame('The ZIP file contains no PDF documents. Only PDF documents are accepted.', $response->getData()->error);
    }
}
