import 'package:flutter/material.dart';
import 'package:intl/intl.dart';
import 'api_service.dart';

class BudgetScreen extends StatefulWidget {
  @override
  _BudgetScreenState createState() => _BudgetScreenState();
}

class _BudgetScreenState extends State<BudgetScreen> {
  List<dynamic> _budgets = [];
  bool _isLoading = true;
  final currencyFormat = NumberFormat.currency(locale: 'id', symbol: 'Rp ', decimalDigits: 0);

  @override
  void initState() {
    super.initState();
    _fetchBudgets();
  }

  Future<void> _fetchBudgets() async {
    final data = await ApiService.getBudgets();
    setState(() {
      _budgets = data;
      _isLoading = false;
    });
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      appBar: AppBar(title: const Text('Anggaran Bulanan', style: TextStyle(fontWeight: FontWeight.bold))),
      body: _isLoading
        ? const Center(child: CircularProgressIndicator())
        : _budgets.isEmpty
          ? const Center(child: Text('Belum ada anggaran yang disetel'))
          : ListView.builder(
              padding: const EdgeInsets.all(16),
              itemCount: _budgets.length,
              itemBuilder: (context, index) {
                final budget = _budgets[index];
                double percent = (budget['spent'] / budget['amount']).clamp(0.0, 1.0);
                Color color = percent > 0.9 ? Colors.red : (percent > 0.7 ? Colors.orange : Colors.green);

                return Card(
                  margin: const EdgeInsets.only(bottom: 16),
                  child: ListTile(
                    title: Text(budget['category']['name'], style: const TextStyle(fontWeight: FontWeight.bold)),
                    subtitle: Column(
                      crossAxisAlignment: CrossAxisAlignment.start,
                      children: [
                        const SizedBox(height: 8),
                        LinearProgressIndicator(value: percent, color: color, backgroundColor: Colors.grey[200]),
                        const SizedBox(height: 8),
                        Text('Terpakai ${currencyFormat.format(budget['spent'])} dari ${currencyFormat.format(budget['amount'])}'),
                      ],
                    ),
                  ),
                );
              },
            ),
    );
  }
}
