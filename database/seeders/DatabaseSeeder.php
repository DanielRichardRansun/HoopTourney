<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\Team;
use App\Models\Tournament;
use App\Models\Player;
use App\Models\Schedule;
use App\Models\MatchResult;
use App\Models\QuarterResult;
use App\Models\TeamStat;
use App\Models\PlayerStat;
use Carbon\Carbon;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // 1. Create Admin User
        $admin = User::create([
            'name' => 'Admin HoopTourney',
            'email' => 'admin@hooptourney.com',
            'password' => Hash::make('password'),
            'role' => 1,
            'email_verified_at' => now(),
        ]);

        // Tournaments Data (4 Tournaments)
        $tournamentsData = [
            [
                'name' => 'Ubaya Engineering Basketball Cup 2024',
                'organizer' => 'BEM Fakultas Teknik Ubaya',
                'description' => 'Kompetisi bola basket antar jurusan se-Fakultas Teknik Universitas Surabaya.',
                'start_date' => Carbon::now()->subYears(2)->subDays(10)->toDateString(),
                'end_date' => Carbon::now()->subYears(2)->addDays(5)->toDateString(),
                'status' => 'completed',
            ],
            [
                'name' => 'Surabaya Student Basketball League 2025',
                'organizer' => 'Night Soldier Hoopers',
                'description' => 'Liga bola basket kompetitif untuk mahasiswa aktif universitas se-Surabaya dan sekitarnya.',
                'start_date' => Carbon::now()->subYears(1)->subDays(15)->toDateString(),
                'end_date' => Carbon::now()->subYears(1)->addDays(5)->toDateString(),
                'status' => 'completed',
            ],
            [
                'name' => 'ClassMeeting SMAN 1 Basketball Edition',
                'organizer' => 'OSIS SMANSA',
                'description' => 'Pertandingan basket antar jurusan dan angkatan tahunan SMAN 1.',
                'start_date' => Carbon::now()->subMonths(6)->subDays(2)->toDateString(),
                'end_date' => Carbon::now()->subMonths(6)->addDays(2)->toDateString(),
                'status' => 'completed',
            ],
            [
                'name' => 'Night Soldier East Java Challenge Series I',
                'organizer' => 'Night Soldier Official',
                'description' => 'Turnamen bergengsi antar komunitas basket amatir se-Jawa Timur putaran pertama.',
                'start_date' => Carbon::now()->addDays(5)->toDateString(), // Upcoming
                'end_date' => Carbon::now()->addDays(15)->toDateString(),
                'status' => 'upcoming',
            ]
        ];

        // Teams Data grouped by Tournament
        $teamsByTournament = [
            // Ubaya Engineering (8 Teams for a great 3-round bracket: Quarter-finals, Semi-finals, Final)
            [
                ['name' => 'Informatika IT', 'coach' => 'Bpk. Hendro', 'manager' => 'Andi'],
                ['name' => 'Teknik Industri', 'coach' => 'Bpk. Budi', 'manager' => 'Bagus'],
                ['name' => 'Teknik Elektro', 'coach' => 'Bpk. Surya', 'manager' => 'Candra'],
                ['name' => 'Teknik Kimia', 'coach' => 'Ibu Rina', 'manager' => 'Deni'],
                ['name' => 'Sistem Informasi', 'coach' => 'Bpk. Anton', 'manager' => 'Eko'],
                ['name' => 'Teknik Manufaktur', 'coach' => 'Bpk. Joko', 'manager' => 'Faisal'],
                ['name' => 'Bioteknologi', 'coach' => 'Ibu Siti', 'manager' => 'Gilang'],
                ['name' => 'Teknik Sipil', 'coach' => 'Bpk. Agus', 'manager' => 'Hadi']
            ],
            // Liga Mahasiswa (4 Teams - Semi-finals, Final)
            [
                ['name' => 'Ubaya Eagles', 'coach' => 'Coach Wira', 'manager' => 'Zaky'],
                ['name' => 'Unair Airlangga', 'coach' => 'Coach Yudi', 'manager' => 'Yusuf'],
                ['name' => 'ITS Spartans', 'coach' => 'Coach Xander', 'manager' => 'Xavi'],
                ['name' => 'Petra Knights', 'coach' => 'Coach Vidi', 'manager' => 'Vian']
            ],
            // ClassMeeting (4 Teams - Semi-finals, Final)
            [
                ['name' => 'XII IPA 1', 'coach' => 'Wali Kelas', 'manager' => 'Ketua Kelas'],
                ['name' => 'XII IPA 2', 'coach' => 'Wali Kelas', 'manager' => 'Ketua Kelas'],
                ['name' => 'XI IPS 1', 'coach' => 'Wali Kelas', 'manager' => 'Ketua Kelas'],
                ['name' => 'X MIPA 3', 'coach' => 'Wali Kelas', 'manager' => 'Ketua Kelas']
            ],
            // Komunitas (4 Teams - Upcoming)
            [
                ['name' => 'Surabaya Hoopers', 'coach' => 'John Doe', 'manager' => 'Jane'],
                ['name' => 'Malang Ballers', 'coach' => 'Joko D', 'manager' => 'Nina'],
                ['name' => 'Sidoarjo Swish', 'coach' => 'Bambang', 'manager' => 'Rini'],
                ['name' => 'Gresik Dunkers', 'coach' => 'Slamet', 'manager' => 'Tono']
            ]
        ];

        $globalTeamIndex = 1;

        foreach ($tournamentsData as $index => $tData) {
            $tournament = Tournament::create([
                'name' => $tData['name'],
                'organizer' => $tData['organizer'],
                'description' => $tData['description'],
                'start_date' => $tData['start_date'],
                'end_date' => $tData['end_date'],
                'status' => $tData['status'],
                'users_id' => $admin->id,
            ]);

            $tournamentTeams = [];

            // Create Teams for this Tournament
            foreach ($teamsByTournament[$index] as $tIndex => $teamData) {
                // Randomly assign logo from logo (1).gif to logo (34).gif
                $teamData['logo'] = 'logo (' . rand(1, 34) . ').gif';
                $team = Team::create($teamData);
                $tournamentTeams[] = $team;

                // Create team owner
                User::create([
                    'name' => 'Manager ' . $team->name,
                    'email' => 'manager' . $globalTeamIndex . '@hooptourney.com',
                    'password' => Hash::make('password'),
                    'role' => 2,
                    'team_id' => $team->id,
                    'email_verified_at' => now(),
                ]);
                $globalTeamIndex++;

                // Attach team to tournament
                $tournament->teams()->attach($team->id);

                // Create initial team stats
                TeamStat::create([
                    'wins' => 0,
                    'losses' => 0,
                    'teams_id' => $team->id,
                    'tournaments_id' => $tournament->id,
                ]);

                // Create 5 Players for this team
                $positions = ['PG', 'SG', 'SF', 'PF', 'C'];
                for ($p = 0; $p < 5; $p++) {
                    Player::create([
                        'name' => 'Player ' . ($p + 1) . ' ' . explode(' ', $team->name)[0],
                        'jersey_number' => array_rand(array_flip([0, 1, 2, 3, 4, 5, 7, 8, 9, 11, 13, 15, 23, 24, 33, 34, 44, 55, 77, 99])),
                        'position' => $positions[$p],
                        'teams_id' => $team->id,
                        // Randomly assign photo from profile (1).jpg to profile (46).jpg
                        'photo' => 'profile (' . rand(1, 46) . ').jpg',
                    ]);
                }
            }

            // Create Schedules and Bracket
            $this->createBracketForTournament($tournament, $tournamentTeams, $index);
        }
    }

    private function createBracketForTournament($tournament, $teams, $tIndex)
    {
        // Use the tournament end date to anchor our completed match dates
        $baseDate = Carbon::parse($tournament->end_date);
        $numTeams = count($teams);
        
        if ($numTeams == 8) {
            // Ubaya Cup Bracket (Completed)
            
            // Quarter Finals
            $qfWinners = [];
            for ($i = 0; $i < 4; $i++) {
                $team1 = $teams[$i * 2];
                $team2 = $teams[($i * 2) + 1];
                
                $match = $this->createMatch($tournament, $team1, $team2, 'Quarter Final', 'completed', $baseDate->copy()->subDays(6 - $i));
                $qfWinners[] = $match['winner'];
            }
            
            // Semi Finals
            $sfWinners = [];
            
            // SF 1
            $sf1Match = $this->createMatch($tournament, $qfWinners[0], $qfWinners[1], 'Semi Final', 'completed', $baseDate->copy()->subDays(3));
            $sfWinners[] = $sf1Match['winner'];
            
            // SF 2
            $sf2Match = $this->createMatch($tournament, $qfWinners[2], $qfWinners[3], 'Semi Final', 'completed', $baseDate->copy()->subDays(2));
            $sfWinners[] = $sf2Match['winner'];
            
            // Final
            $this->createMatch($tournament, $sfWinners[0], $sfWinners[1], 'Final', 'completed', $baseDate->copy()->subDays(0));
            
        } else if ($numTeams == 4 && $tournament->status == 'completed') {
            // 4 Teams bracket (Completed)
            // Semi Finals
            $sfWinners = [];
            for ($i = 0; $i < 2; $i++) {
                $team1 = $teams[$i * 2];
                $team2 = $teams[($i * 2) + 1];
                
                $match = $this->createMatch($tournament, $team1, $team2, 'Semi Final', 'completed', $baseDate->copy()->subDays(2 - $i));
                $sfWinners[] = $match['winner'];
            }
            
            // Final
            $this->createMatch($tournament, $sfWinners[0], $sfWinners[1], 'Final', 'completed', $baseDate->copy()->subDays(0));
            
        } else if ($numTeams == 4 && $tournament->status == 'upcoming') {
            $startDate = Carbon::parse($tournament->start_date);
            // Semi Finals (Scheduled)
            for ($i = 0; $i < 2; $i++) {
                $team1 = $teams[$i * 2];
                $team2 = $teams[($i * 2) + 1];
                
                $this->createMatch($tournament, $team1, $team2, 'Semi Final', 'scheduled', $startDate->copy()->addDays($i + 1));
            }
        }
    }

    private function createMatch($tournament, $team1, $team2, $round, $status, $date)
    {
        $schedule = Schedule::create([
            'team1_id' => $team1->id,
            'team2_id' => $team2->id,
            'date' => $date,
            'location' => 'Lapangan Basket Utama',
            'tournaments_id' => $tournament->id,
            'status' => $status,
            'round' => $round,
        ]);

        if ($status == 'completed') {
            // Randomly pick winner
            $team1Wins = (rand(0, 1) == 0);
            $winner = $team1Wins ? $team1 : $team2;
            $loser = $team1Wins ? $team2 : $team1;
            
            // Close game, e.g., 85 - 82
            $winnerScore = rand(75, 100);
            $loserScore = $winnerScore - rand(2, 15);
            
            $matchResult = MatchResult::create([
                'team1_score' => $team1Wins ? $winnerScore : $loserScore,
                'team2_score' => $team1Wins ? $loserScore : $winnerScore,
                'winning_team_id' => $winner->id,
                'losing_team_id' => $loser->id,
                'schedules_id' => $schedule->id,
            ]);
            
            // Update Team Stats
            TeamStat::where('teams_id', $winner->id)->where('tournaments_id', $tournament->id)->increment('wins');
            TeamStat::where('teams_id', $loser->id)->where('tournaments_id', $tournament->id)->increment('losses');

            // Quarter Results
            $t1QScoreRem = $team1Wins ? $winnerScore : $loserScore;
            $t2QScoreRem = $team1Wins ? $loserScore : $winnerScore;
            
            for ($q = 1; $q <= 4; $q++) {
                $maxT1 = $q == 4 ? $t1QScoreRem : min(30, intval($t1QScoreRem / (5 - $q)) + rand(-5, 5));
                $maxT2 = $q == 4 ? $t2QScoreRem : min(30, intval($t2QScoreRem / (5 - $q)) + rand(-5, 5));
                
                QuarterResult::create([
                    'match_results_id' => $matchResult->id,
                    'quarter_number' => $q,
                    'team1_score' => $maxT1,
                    'team2_score' => $maxT2,
                ]);
                
                $t1QScoreRem -= $maxT1;
                $t2QScoreRem -= $maxT2;
            }

            // Player Stats
            $this->generatePlayerStats($team1, $matchResult, $team1Wins ? $winnerScore : $loserScore);
            $this->generatePlayerStats($team2, $matchResult, $team1Wins ? $loserScore : $winnerScore);

            return [
                'schedule' => $schedule,
                'matchResult' => $matchResult,
                'winner' => $winner,
                'loser' => $loser
            ];
        }

        return ['schedule' => $schedule];
    }
    
    private function generatePlayerStats($team, $matchResult, $totalTeamPoints)
    {
        $remainingPoints = $totalTeamPoints;
        $players = $team->players;
        
        foreach ($players as $index => $player) {
            $isLastPlayer = ($index == count($players) - 1);
            $points = $isLastPlayer ? max(0, $remainingPoints) : rand(0, min(30, $remainingPoints));
            
            // Derive stats somewhat logically based on points
            $fgm = intval($points / 2.5) + rand(0, 2);
            $ftm = $points - ($fgm * 2) - rand(0, 2); // simplistic derivation
            if ($ftm < 0) $ftm = 0;
            
            $fga = $fgm + rand(1, 10);
            $fta = $ftm + rand(0, 4);

            PlayerStat::create([
                'players_id' => $player->id,
                'match_results_id' => $matchResult->id,
                'quarter_number' => 0, // 0 means total match sum
                'per' => rand(5, 25) + (rand(0, 99) / 100),
                'point' => $points,
                'fgm' => $fgm,
                'fga' => $fga,
                'fta' => $fta,
                'ftm' => $ftm,
                'orb' => rand(0, 5),
                'drb' => rand(1, 10),
                'stl' => rand(0, 4),
                'ast' => rand(0, 8),
                'blk' => rand(0, 3),
                'pf' => rand(0, 5),
                'to' => rand(0, 5),
            ]);
            
            $remainingPoints -= $points;
        }
    }
}
