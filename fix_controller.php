<?php

$content = file_get_contents('app/Http/Controllers/BankIntegrationController.php');

// Update create method
$content = str_replace(
    '    public function create()
    {
        return view(\'bank-integrations.create\');
    }',
    '    public function create()
    {
        $accounts = Auth::user()->accounts;
        return view(\'bank-integrations.create\', compact(\'accounts\'));
    }',
    $content
);

// Update edit method
$content = str_replace(
    '    public function edit(BankIntegration $bankIntegration)
    {
        $this->ensureOwner($bankIntegration);
        return view(\'bank-integrations.edit\', compact(\'bankIntegration\'));
    }',
    '    public function edit(BankIntegration $bankIntegration)
    {
        $this->ensureOwner($bankIntegration);
        $accounts = Auth::user()->accounts;
        return view(\'bank-integrations.edit\', compact(\'bankIntegration\', \'accounts\'));
    }',
    $content
);

// Update store validation
$content = str_replace(
    '        $request->validate([
            \'bank_name\' => \'required|string|max:255\',
            \'account_number\' => \'nullable|string|max:255\',
            \'account_type\' => \'required|in:checking,savings,credit_card\',
            \'integration_type\' => \'required|in:api,csv,ofx,manual\',
            \'credentials\' => \'nullable|array\',
            \'settings\' => \'nullable|array\',
            \'is_active\' => \'boolean\',
            \'notes\' => \'nullable|string\',
        ]);',
    '        $request->validate([
            \'account_id\' => \'required|exists:accounts,id\',
            \'bank_name\' => \'required|string|max:255\',
            \'account_number\' => \'nullable|string|max:255\',
            \'account_type\' => \'required|in:checking,savings,credit_card\',
            \'integration_type\' => \'required|in:api,csv,ofx,manual\',
            \'credentials\' => \'nullable|array\',
            \'settings\' => \'nullable|array\',
            \'is_active\' => \'boolean\',
            \'notes\' => \'nullable|string\',
        ]);',
    $content
);

// Update store create array
$content = str_replace(
    '        BankIntegration::create([
            \'user_id\' => Auth::id(),
            \'bank_name\' => $request->bank_name,
            \'account_number\' => $request->account_number,
            \'account_type\' => $request->account_type,
            \'integration_type\' => $request->integration_type,
            \'credentials\' => $request->credentials,
            \'settings\' => $request->settings,
            \'is_active\' => $request->boolean(\'is_active\', true),
            \'notes\' => $request->notes,
        ]);',
    '        BankIntegration::create([
            \'user_id\' => Auth::id(),
            \'account_id\' => $request->account_id,
            \'bank_name\' => $request->bank_name,
            \'account_number\' => $request->account_number,
            \'account_type\' => $request->account_type,
            \'integration_type\' => $request->integration_type,
            \'credentials\' => $request->credentials,
            \'settings\' => $request->settings,
            \'is_active\' => $request->boolean(\'is_active\', true),
            \'notes\' => $request->notes,
        ]);',
    $content
);

// Update update validation
$content = str_replace(
    '        $request->validate([
            \'bank_name\' => \'required|string|max:255\',
            \'account_number\' => \'nullable|string|max:255\',
            \'account_type\' => \'required|in:checking,savings,credit_card\',
            \'integration_type\' => \'required|in:api,csv,ofx,manual\',
            \'credentials\' => \'nullable|array\',
            \'settings\' => \'nullable|array\',
            \'is_active\' => \'boolean\',
            \'notes\' => \'nullable|string\',
        ]);',
    '        $request->validate([
            \'account_id\' => \'required|exists:accounts,id\',
            \'bank_name\' => \'required|string|max:255\',
            \'account_number\' => \'nullable|string|max:255\',
            \'account_type\' => \'required|in:checking,savings,credit_card\',
            \'integration_type\' => \'required|in:api,csv,ofx,manual\',
            \'credentials\' => \'nullable|array\',
            \'settings\' => \'nullable|array\',
            \'is_active\' => \'boolean\',
            \'notes\' => \'nullable|string\',
        ]);',
    $content
);

// Update update array
$content = str_replace(
    '        $bankIntegration->update([
            \'bank_name\' => $request->bank_name,
            \'account_number\' => $request->account_number,
            \'account_type\' => $request->account_type,
            \'integration_type\' => $request->integration_type,
            \'credentials\' => $request->credentials,
            \'settings\' => $request->settings,
            \'is_active\' => $request->boolean(\'is_active\', true),
            \'notes\' => $request->notes,
        ]);',
    '        $bankIntegration->update([
            \'account_id\' => $request->account_id,
            \'bank_name\' => $request->bank_name,
            \'account_number\' => $request->account_number,
            \'account_type\' => $request->account_type,
            \'integration_type\' => $request->integration_type,
            \'credentials\' => $request->credentials,
            \'settings\' => $request->settings,
            \'is_active\' => $request->boolean(\'is_active\', true),
            \'notes\' => $request->notes,
        ]);',
    $content
);

file_put_contents('app/Http/Controllers/BankIntegrationController.php', $content);
echo 'Controller updated successfully';
