<?php

namespace App\Http\Controllers\Admin;

use App\Models\Question;
use App\Traits\ManagesModelsTrait;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\QuestionRequest;
use App\Http\Resources\Admin\QuestionResource;


class QuestionController extends Controller
{
    use ManagesModelsTrait;

    public function showAll()
  {
      $this->authorize('manage_users');

      $Questions = Question::get();
      return response()->json([
          'data' => QuestionResource::collection($Questions),
          'message' => "Show All Questions Successfully."
      ]);
  }





  public function create(QuestionRequest $request)
  {
      $this->authorize('manage_users');

         $Question =Question::create ([
                 'question' => $request-> question,
                 'exam_id' => $request-> exam_id,
                 'choice_1' => $request-> choice_1,
                'choice_2' => $request-> choice_2,
                'choice_3' => $request-> choice_3,
                'choice_4' => $request-> choice_4,
                'correct_choice' => $request-> correct_choice
          ]);
        //   $Question->load('exam');

          $exam = $Question->exam;
          $exam->numOfQ = $exam->questions()->count();
          $exam->save();

        //  $Question->save();
         return response()->json([
          'data' =>new QuestionResource($Question),
          'message' => "Question Created Successfully."
      ]);
      }


  public function edit(string $id)
  {
      $this->authorize('manage_users');
      $Question = Question::with('exam')->find($id);

      if (!$Question) {
          return response()->json([
              'message' => "Question not found."
          ], 404);
      }

      return response()->json([
          'data' =>new QuestionResource($Question),
          'message' => "Edit Question By ID Successfully."
      ]);
  }



  public function update(QuestionRequest $request, string $id)
  {
      $this->authorize('manage_users');
     $Question =Question::findOrFail($id);

     if (!$Question) {
      return response()->json([
          'message' => "Question not found."
      ], 404);
  }
     $Question->update([
        'question' => $request-> question,
        'exam_id' => $request-> exam_id,
        'choice_1' => $request-> choice_1,
        'choice_2' => $request-> choice_2,
        'choice_3' => $request-> choice_3,
        'choice_4' => $request-> choice_4,
        'correct_choice' => $request-> correct_choice
      ]);
      $Question->load('exam');

     $Question->save();
     return response()->json([
      'data' =>new QuestionResource($Question),
      'message' => " Update Question By Id Successfully."
  ]);

}

  public function destroy(string $id)
  {
      return $this->destroyModel(Question::class, QuestionResource::class, $id);
  }

  public function showDeleted(){
    $this->authorize('manage_users');
$Questions=Question::with('exam')->onlyTrashed()->get();
return response()->json([
    'data' =>QuestionResource::collection($Questions),
    'message' => "Show Deleted Questions Successfully."
]);
}

public function restore(string $id)
{
   $this->authorize('manage_users');
$Question = Question::with('exam')->withTrashed()->where('id', $id)->first();
if (!$Question) {
    return response()->json([
        'message' => "Question not found."
    ], 404);
}
$Question->restore();
return response()->json([
    'data' =>new QuestionResource($Question),
    'message' => "Restore Question By Id Successfully."
]);
}

  public function forceDelete(string $id)
  {
      return $this->forceDeleteModel(Question::class, $id);
  }
}
