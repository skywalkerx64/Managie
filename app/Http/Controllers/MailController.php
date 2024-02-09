<?php

namespace App\Http\Controllers;

use App\Http\Requests\Mail\SearchMailRequest;
use App\Http\Requests\Mail\SendMailRequest;
use App\Models\Mail;
use App\Models\Candidate;
use App\Models\Application;
use App\Models\Recruitment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use App\Services\Notification\MailObject;
use App\Http\Requests\Mail\StoreMailRequest;
use App\Http\Requests\Mail\UpdateMailRequest;
use App\Http\Resources\Mail\MailListResource;
use App\Http\Resources\Mail\MailShowResource;
use Symfony\Component\HttpFoundation\Response;
use App\Services\Notification\NotificationService;

class MailController extends Controller
{
    public function index()
    {
        abort_if(Gate::denies('mail_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return MailListResource::collection(Mail::paginate(10));
    }

    public function store(StoreMailRequest $request)
    {
        $mail = Mail::create([
            ...$request->all()
        ]);
        
        $files = [];

        if($request->medias != null)
        {
            if($request->has('medias'))
            {
                foreach($request->medias as $media)
                {
                    $mail->addMedia($media)->toMediaCollection(Mail::FILES_COLLECTION_NAME);
                }
            }
    
            foreach($mail->media as $file)
            {
              $files[] = $file->getPath();
            }
        }

        (new NotificationService)->toEmails([env("CONTACT_MAIL", "mtca.dsisa@gouv.bj")])->sendMail(new MailObject(
          subject: $request->subject,
          preheeader: trans("mails.new_common_mail"),
          intro: trans("mails.new_common_mail"),
          corpus: $request->content,
          files: $files
        ));


        return (new MailShowResource($mail))
            ->response()
            ->setStatusCode(Response::HTTP_CREATED);
    }

    public function search(SearchMailRequest $request)
    {
        $type = $request->type;
        $per_page = $request->per_page;

        $mails = Mail::query();

        if($type)
        {
            $mails = $mails->where('type', $type);
        }

        return MailListResource::collection($mails->paginate($per_page));
    }

    public function show(Mail $mail)
    {
        abort_if(Gate::denies('mail_show'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return new MailShowResource($mail);
    }

    public function update(UpdateMailRequest $request, Mail $mail)
    {
        $mail->update($request->all());

        return (new MailShowResource($mail))
            ->response()
            ->setStatusCode(Response::HTTP_ACCEPTED);
    }

    public function destroy(Mail $mail)
    {
        abort_if(Gate::denies('mail_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $mail->delete();

        return response(null, Response::HTTP_NO_CONTENT);
    }

    public function types()
    {
        return Mail::TYPES;
    }

    public function send(SendMailRequest $request)
    {
        $mail = Mail::create([
            ...$request->all(),
            'type' => Mail::TYPE_SENT
        ]);
        
        $files = [];

        if($request->medias != null)
        {
            if($request->has('medias'))
            {
                foreach($request->medias as $media)
                {
                    $mail->addMedia($media)->toMediaCollection(Mail::FILES_COLLECTION_NAME);
                }
            }
    
            foreach($mail->media as $file)
            {
              $files[] = $file->getPath();
            }
        }

        (new NotificationService)->toEmails([$request->email])->withCCMails($request->cc ?? [])->withBCCMails($request->bcc ?? [])->sendMail(new MailObject(
          subject: $request->subject ?? "",
          preheeader: trans("mails.new_common_mail"),
          intro: trans("mails.new_common_mail"),
          corpus: $request->content ?? "",
          files: $files ?? []
        ));
        return response()->json(["Mail sent successfully"], Response::HTTP_OK);
    }
}
