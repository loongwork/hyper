<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class FaweController extends Controller
{
    public function upload(Request $request)
    {
        $id = null;
        foreach ($request->input() as $key => $value) {
            if (is_null($value) && preg_match('/\w{8}-\w{4}-\w{4}-\w{4}-\w{12}/', $key)) {
                $id = $key;
                break;
            }
        }
        if (is_null($id)) {
            return response('No id found', 400);
        }

        $file = $request->file('schematicFile');
        if (!str_ends_with($file->getClientOriginalName(), '.schematic')) {
            return response('Invalid file type', 400);
        }

        $file->storeAs('schematics', $id . '.schematic', 'oss');
        $path = Storage::disk('oss')->url('schematics/' . $id . '.schematic');
        $original_path = sprintf('%s.%s', config('filesystems.disks.oss.bucket'), config('filesystems.disks.oss.endpoint'));
        $path = str_replace($original_path, config('filesystems.disks.oss.cdn_url'), $path);

        return response("文件上传成功，请点击以下链接以下载：{$path}");
    }
}
