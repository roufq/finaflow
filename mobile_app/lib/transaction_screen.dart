import 'package:flutter/material.dart';
import 'api_service.dart';

class TransactionScreen extends StatefulWidget {
  @override
  _TransactionScreenState createState() => _TransactionScreenState();
}

class _TransactionScreenState extends State<TransactionScreen> {
  final _formKey = GlobalKey<FormState>();
  final TextEditingController _amountController = TextEditingController();
  final TextEditingController _descriptionController = TextEditingController();

  String _type = 'expense';
  String? _selectedCategoryId;
  String? _selectedAccountId;

  List<dynamic> _categories = [];
  List<dynamic> _accounts = [];
  bool _isLoading = false;
  bool _isFetchingInitialData = true;

  @override
  void initState() {
    super.initState();
    _loadInitialData();
  }

  void _loadInitialData() async {
    try {
      final cats = await ApiService.getCategories();
      final accs = await ApiService.getAccounts();
      setState(() {
        _categories = cats;
        _accounts = accs;
        if (_categories.isNotEmpty) _selectedCategoryId = _categories.first['id'].toString();
        if (_accounts.isNotEmpty) _selectedAccountId = _accounts.first['id'].toString();
        _isFetchingInitialData = false;
      });
    } catch (e) {
      setState(() => _isFetchingInitialData = false);
    }
  }

  void _submit() async {
    if (!_formKey.currentState!.validate() || _selectedCategoryId == null || _selectedAccountId == null) {
      ScaffoldMessenger.of(context).showSnackBar(SnackBar(content: Text('Lengkapi semua data')));
      return;
    }

    setState(() => _isLoading = true);

    final result = await ApiService.addTransaction({
      'amount': _amountController.text.replaceAll('.', ''),
      'description': _descriptionController.text,
      'type': _type,
      'category_id': _selectedCategoryId,
      'account_id': _selectedAccountId,
      'date': DateTime.now().toIso8601String().split('T')[0],
    });

    setState(() => _isLoading = false);

    if (result['success'] == true) {
      ScaffoldMessenger.of(context).showSnackBar(
        SnackBar(content: Text('Transaksi berhasil disimpan!'), backgroundColor: Colors.green),
      );
      Navigator.pop(context, true);
    } else {
      ScaffoldMessenger.of(context).showSnackBar(
        SnackBar(content: Text(result['message'] ?? 'Gagal menyimpan transaksi'), backgroundColor: Colors.red),
      );
    }
  }

  @override
  Widget build(BuildContext context) {
    if (_isFetchingInitialData) {
      return Scaffold(body: Center(child: CircularProgressIndicator()));
    }

    return Scaffold(
      appBar: AppBar(
        title: Text('Tambah Transaksi', style: TextStyle(fontWeight: FontWeight.bold)),
        backgroundColor: Colors.white,
        foregroundColor: Colors.black,
        elevation: 0,
      ),
      body: SingleChildScrollView(
        padding: const EdgeInsets.all(24.0),
        child: Form(
          key: _formKey,
          child: Column(
            crossAxisAlignment: CrossAxisAlignment.stretch,
            children: [
              SegmentedButton<String>(
                segments: const [
                  ButtonSegment(value: 'expense', label: Text('Pengeluaran'), icon: Icon(Icons.upload)),
                  ButtonSegment(value: 'income', label: Text('Pemasukan'), icon: Icon(Icons.download)),
                ],
                selected: {_type},
                onSelectionChanged: (newSelection) {
                  setState(() => _type = newSelection.first);
                },
              ),
              SizedBox(height: 32),

              // Input Nominal
              TextFormField(
                controller: _amountController,
                keyboardType: TextInputType.number,
                style: TextStyle(fontSize: 24, fontWeight: FontWeight.bold),
                decoration: InputDecoration(
                  labelText: 'Nominal',
                  prefixText: 'Rp ',
                  border: OutlineInputBorder(borderRadius: BorderRadius.circular(16)),
                ),
                validator: (value) => value == null || value.isEmpty ? 'Masukkan nominal' : null,
              ),
              SizedBox(height: 20),

              // Dropdown Akun
              DropdownButtonFormField<String>(
                value: _selectedAccountId,
                decoration: InputDecoration(
                  labelText: 'Pilih Akun',
                  border: OutlineInputBorder(borderRadius: BorderRadius.circular(16)),
                  prefixIcon: Icon(Icons.account_balance),
                ),
                items: _accounts.map((acc) {
                  return DropdownMenuItem<String>(
                    value: acc['id'].toString(),
                    child: Text(acc['name']),
                  );
                }).toList(),
                onChanged: (val) => setState(() => _selectedAccountId = val),
              ),
              SizedBox(height: 20),

              // Dropdown Kategori
              DropdownButtonFormField<String>(
                value: _selectedCategoryId,
                decoration: InputDecoration(
                  labelText: 'Kategori',
                  border: OutlineInputBorder(borderRadius: BorderRadius.circular(16)),
                  prefixIcon: Icon(Icons.category),
                ),
                items: _categories.map((cat) {
                  return DropdownMenuItem<String>(
                    value: cat['id'].toString(),
                    child: Text(cat['name']),
                  );
                }).toList(),
                onChanged: (val) => setState(() => _selectedCategoryId = val),
              ),
              SizedBox(height: 20),

              TextFormField(
                controller: _descriptionController,
                decoration: InputDecoration(
                  labelText: 'Keterangan',
                  border: OutlineInputBorder(borderRadius: BorderRadius.circular(16)),
                ),
                validator: (value) => value == null || value.isEmpty ? 'Masukkan keterangan' : null,
              ),
              SizedBox(height: 40),

              ElevatedButton(
                onPressed: _isLoading ? null : _submit,
                style: ElevatedButton.styleFrom(
                  backgroundColor: _type == 'income' ? Colors.green : Colors.blueAccent,
                  padding: EdgeInsets.symmetric(vertical: 18),
                  shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(16)),
                ),
                child: _isLoading
                  ? CircularProgressIndicator(color: Colors.white)
                  : Text('Simpan Transaksi', style: TextStyle(fontSize: 16, color: Colors.white, fontWeight: FontWeight.bold)),
              ),
            ],
          ),
        ),
      ),
    );
  }
}
