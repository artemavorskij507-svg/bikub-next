<?php
namespace App\Http\Controllers;
use Spatie\MediaLibrary\MediaCollections\Models\Media;
class AdminSupportAttachmentDownloadController extends Controller {
 public function __invoke(Media $media){
  abort_unless(auth()->user()?->can('admin.support.attachments'),403);
  abort_unless(in_array($media->collection_name,['support_ticket_attachments','support_message_attachments'],true),404);
  abort_unless(is_file($media->getPath()),404);
  activity()->causedBy(auth()->user())->performedOn($media->model)->withProperties(['media_id'=>$media->id,'filename'=>$media->file_name])->log('support_attachment.downloaded');
  return response()->download($media->getPath(),$media->file_name);
 }
}
