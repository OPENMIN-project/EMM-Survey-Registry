<?php

namespace App\Console\Commands;

use App\FieldOption;
use App\User;
use Illuminate\Console\Command;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;

class MatchUsersToValues extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'ethmig:match-users';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Matches users to values from field-options for f_11_1';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $users = User::select('id', 'name')->get();
        $options = FieldOption::whereSurveyFieldId(242)->get();

        $results = $options->mapWithKeys(function ($option) use ($users) {
            $results = $users->mapWithKeys(function ($user) use ($option) {
                $text1 = strtolower(Str::replaceArray('-', [' '], $user->name));
                $text2 = strtolower($option->value);
                return [
                    $user->name => [
                        'similarity' => levenshtein(
                            $text1,
                            $text2,
                            1, 1, 0
                        ),
                        'name' => $user->name,
                        'id' => $user->id
                    ]
                ];
            });

            /** @var Collection $results */
            return [
                $option->value => $results->sortBy('similarity')
                    ->filter(fn($match) => $match['similarity'] <= 3)
                    ->first()
            ];
        });

        dump($results->toJson());
        return 0;
    }
}
