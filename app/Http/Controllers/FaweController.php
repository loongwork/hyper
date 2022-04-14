<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class FaweController extends Controller
{
    /**
     * FAWE 上传文件
     */
    public function upload(Request $request): Response
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

        $this->log('schematic uploaded and saved to ' . $path, compact('id', 'path'));

        return response("文件上传成功，请点击以下链接以下载：{$path}\n请忽视下方的链接，那是插件本身的设计缺陷，无法去除。");
    }

    /**
     * FAWE 下载文件
     */
    public function download(Request $request, string $file = null): Response|RedirectResponse
    {
        // 文件为空代表是 FAWE 远程加载请求
        if (!is_null($file)) {
            $this->log('schematic fetched and returned', compact('file'));
            return response(file_get_contents("https://fawe.escraft.net/faweupload/uploads/{$file}"));
//            return Storage::disk('oss')->download("schematics/$file");
        }

        // 或者为用户下载请求

        $id = $request->query('key');
        $type = $request->query('type');

        if ($type !== 'schematic') {
            return response('Invalid file type', 400);
        }

        $this->log('schematic download request redirected', compact('id', 'type'));

        return response()->redirectTo(sprintf(
            'http://%s/%s/%s',
            config('filesystems.disks.oss.cdn_url'),
            Str::plural($type),
            $id . '.' . $type
        ));
    }

    /**
     * 记录日志
     */
    private function log(string $message, array $data = null): void
    {
        $log = '[FAWE] ' . $message;
        logger()->info($log, $data);
    }
}
