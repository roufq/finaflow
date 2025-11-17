@extends('layouts.app')

@section('content')
<div class="container-fluid">

    <!-- Page Heading -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Financial Personality Quiz</h1>
    </div>

    <div class="row">
        <div class="col-lg-8">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Discover Your Financial Personality</h6>
                </div>
                <div class="card-body">
                    <p class="mb-4">Answer these questions to understand your financial behavior patterns and get personalized recommendations.</p>

                    <form id="personality-quiz" method="POST" action="{{ route('behavioral.personality.quiz.store') }}">
                        @csrf

                        <!-- Question 1 -->
                        <div class="question mb-4">
                            <h5>1. When you receive a bonus or unexpected income, what do you typically do?</h5>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="q1" id="q1a" value="spender" required>
                                <label class="form-check-label" for="q1a">
                                    Spend it immediately on something fun
                                </label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="q1" id="q1b" value="saver">
                                <label class="form-check-label" for="q1b">
                                    Save most of it for future security
                                </label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="q1" id="q1c" value="investor">
                                <label class="form-check-label" for="q1c">
                                    Invest it for long-term growth
                                </label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="q1" id="q1d" value="avoider">
                                <label class="form-check-label" for="q1d">
                                    Pay off debts or bills
                                </label>
                            </div>
                        </div>

                        <!-- Question 2 -->
                        <div class="question mb-4">
                            <h5>2. How comfortable are you with financial risk?</h5>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="q2" id="q2a" value="1" required>
                                <label class="form-check-label" for="q2a">
                                    Very conservative - I prefer guaranteed returns
                                </label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="q2" id="q2b" value="5">
                                <label class="form-check-label" for="q2b">
                                    Moderate - Some risk is okay for better returns
                                </label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="q2" id="q2c" value="10">
                                <label class="form-check-label" for="q2c">
                                    Very aggressive - High risk for high rewards
                                </label>
                            </div>
                        </div>

                        <!-- Question 3 -->
                        <div class="question mb-4">
                            <h5>3. When shopping, you typically:</h5>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="q3" id="q3a" value="impulsive" required>
                                <label class="form-check-label" for="q3a">
                                    Buy what you want when you see it
                                </label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="q3" id="q3b" value="planned">
                                <label class="form-check-label" for="q3b">
                                    Plan purchases and stick to a budget
                                </label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="q3" id="q3c" value="research">
                                <label class="form-check-label" for="q3c">
                                    Research and compare prices extensively
                                </label>
                            </div>
                        </div>

                        <!-- Question 4 -->
                        <div class="question mb-4">
                            <h5>4. Your approach to saving money is:</h5>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="q4" id="q4a" value="minimal" required>
                                <label class="form-check-label" for="q4a">
                                    I save what's left after spending
                                </label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="q4" id="q4b" value="automatic">
                                <label class="form-check-label" for="q4b">
                                    I set up automatic transfers to savings
                                </label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="q4" id="q4c" value="strategic">
                                <label class="form-check-label" for="q4c">
                                    I have multiple savings goals and strategies
                                </label>
                            </div>
                        </div>

                        <!-- Question 5 -->
                        <div class="question mb-4">
                            <h5>5. How often do you review your financial situation?</h5>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="q5" id="q5a" value="rarely" required>
                                <label class="form-check-label" for="q5a">
                                    Rarely - only when there's a problem
                                </label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="q5" id="q5b" value="monthly">
                                <label class="form-check-label" for="q5b">
                                    Monthly or when bills are due
                                </label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="q5" id="q5c" value="weekly">
                                <label class="form-check-label" for="q5c">
                                    Weekly or more frequently
                                </label>
                            </div>
                        </div>

                        <button type="submit" class="btn btn-success btn-lg">Submit Quiz</button>
                        <a href="{{ route('behavioral.index') }}" class="btn btn-secondary btn-lg ml-2">Cancel</a>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">What You'll Learn</h6>
                </div>
                <div class="card-body">
                    <ul class="small">
                        <li>Your dominant financial personality type</li>
                        <li>Risk tolerance level (1-10 scale)</li>
                        <li>Spending and saving patterns</li>
                        <li>Personalized financial recommendations</li>
                        <li>Strategies to improve financial habits</li>
                    </ul>
                </div>
            </div>

            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Personality Types</h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-12">
                            <h6>🛍️ Spender</h6>
                            <p class="small text-muted">Enjoys spending and experiences</p>
                        </div>
                    </div>
                    <div class="row mt-2">
                        <div class="col-12">
                            <h6>💰 Saver</h6>
                            <p class="small text-muted">Prioritizes security and stability</p>
                        </div>
                    </div>
                    <div class="row mt-2">
                        <div class="col-12">
                            <h6>📈 Investor</h6>
                            <p class="small text-muted">Focuses on growth and returns</p>
                        </div>
                    </div>
                    <div class="row mt-2">
                        <div class="col-12">
                            <h6>🛡️ Avoider</h6>
                            <p class="small text-muted">Prefers to avoid financial decisions</p>
                        </div>
                    </div>
                    <div class="row mt-2">
                        <div class="col-12">
                            <h6>⚖️ Balanced</h6>
                            <p class="small text-muted">Maintains healthy financial balance</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

</div>

<script>
document.getElementById('personality-quiz').addEventListener('submit', function(e) {
    // Calculate personality type based on answers
    const formData = new FormData(this);
    const answers = {};

    for (let [key, value] of formData.entries()) {
        answers[key] = value;
    }

    // Determine personality type
    let personalityType = 'balanced';
    const q1Answer = answers.q1;

    if (q1Answer === 'spender') {
        personalityType = 'spender';
    } else if (q1Answer === 'saver') {
        personalityType = 'saver';
    } else if (q1Answer === 'investor') {
        personalityType = 'investor';
    } else if (q1Answer === 'avoider') {
        personalityType = 'avoider';
    }

    // Add hidden fields for processing
    const personalityInput = document.createElement('input');
    personalityInput.type = 'hidden';
    personalityInput.name = 'personality_type';
    personalityInput.value = personalityType;
    this.appendChild(personalityInput);

    const riskToleranceInput = document.createElement('input');
    riskToleranceInput.type = 'hidden';
    riskToleranceInput.name = 'risk_tolerance';
    riskToleranceInput.value = answers.q2 || 5;
    this.appendChild(riskToleranceInput);

    const spendingStyleInput = document.createElement('input');
    spendingStyleInput.type = 'hidden';
    spendingStyleInput.name = 'spending_style';
    spendingStyleInput.value = answers.q3 || 'planned';
    this.appendChild(spendingStyleInput);

    const savingHabitsInput = document.createElement('input');
    savingHabitsInput.type = 'hidden';
    savingHabitsInput.name = 'saving_habits';
    savingHabitsInput.value = answers.q4 || 'automatic';
    this.appendChild(savingHabitsInput);

    const scoresInput = document.createElement('input');
    scoresInput.type = 'hidden';
    scoresInput.name = 'scores';
    scoresInput.value = JSON.stringify(answers);
    this.appendChild(scoresInput);
});
</script>
@endsection
