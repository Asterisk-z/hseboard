<?php

namespace Database\Seeders;

use App\Models\InvestigationQuestion as ModelsInvestigationQuestion;
use Illuminate\Database\Seeder;

class InvestigationQuestion extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {

        $priorities = [
            [
                'question' => 'What happened?',
            ],
            [
                'question' => 'Where did it happen?',
            ],
            [
                'question' => 'What date and time did it happen?',
            ],
            [
                'question' => 'How did it happen?',
            ],
            [
                'question' => 'Where were you at the time of the incident?',
            ],
            [
                'question' => 'Are there injured workers? If yes, how many? Describe their conditions/injuries.',
            ],
            [
                'question' => 'In your opinion, what caused the incident?',
            ],
            [
                'question' => 'How do you think this can be prevented next time?',
            ],
        ];

        foreach ($priorities as $priority) {
            if (ModelsInvestigationQuestion::where('question', $priority['question'])->exists()) {
                continue;
            }
            ModelsInvestigationQuestion::create($priority);
        }

    }
}
