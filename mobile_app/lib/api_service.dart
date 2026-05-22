import 'dart:convert';
import 'package:http/http.dart' as http;
import 'package:shared_preferences/shared_preferences.dart';

class ApiService {
  static const String baseUrl = 'http://10.0.2.2:8000/api/v1';

  static Future<Map<String, String>> _headers() async {
    final prefs = await SharedPreferences.getInstance();
    final token = prefs.getString('auth_token');
    return {
      'Accept': 'application/json',
      'Content-Type': 'application/json',
      'Authorization': 'Bearer $token',
    };
  }

  static Future<Map<String, dynamic>> login(String email, String password) async {
    try {
      final response = await http.post(
        Uri.parse('$baseUrl/login'),
        headers: {'Accept': 'application/json'},
        body: {'email': email, 'password': password, 'device_name': 'android_mobile'},
      ).timeout(const Duration(seconds: 15));

      final data = json.decode(response.body);
      if (response.statusCode == 200) {
        final prefs = await SharedPreferences.getInstance();
        await prefs.setString('auth_token', data['data']['token']);
        return {'success': true};
      }
      return {'success': false, 'message': data['message'] ?? 'Login Gagal'};
    } catch (e) {
      return {'success': false, 'message': 'Kesalahan koneksi ke server Laravel'};
    }
  }

  static Future<void> logout() async {
    final prefs = await SharedPreferences.getInstance();
    await prefs.remove('auth_token');
  }

  static Future<Map<String, dynamic>> getDashboard() async {
    final response = await http.get(Uri.parse('$baseUrl/dashboard'), headers: await _headers());
    return json.decode(response.body);
  }

  static Future<List<dynamic>> getTransactions() async {
    final response = await http.get(Uri.parse('$baseUrl/transactions'), headers: await _headers());
    if (response.statusCode == 200) return json.decode(response.body)['data'];
    return [];
  }

  static Future<Map<String, dynamic>> addTransaction(Map<String, dynamic> data) async {
    final response = await http.post(
      Uri.parse('$baseUrl/transactions'),
      headers: await _headers(),
      body: json.encode(data),
    );
    return {'success': response.statusCode == 201 || response.statusCode == 200, 'message': json.decode(response.body)['message']};
  }

  static Future<bool> deleteTransaction(int id) async {
    final response = await http.delete(Uri.parse('$baseUrl/transactions/$id'), headers: await _headers());
    return response.statusCode == 204 || response.statusCode == 200;
  }

  static Future<List<dynamic>> getGoals() async {
    final response = await http.get(Uri.parse('$baseUrl/goals'), headers: await _headers());
    if (response.statusCode == 200) return json.decode(response.body)['data'];
    return [];
  }

  static Future<List<dynamic>> getBudgets() async {
    final response = await http.get(Uri.parse('$baseUrl/budgets'), headers: await _headers());
    if (response.statusCode == 200) return json.decode(response.body)['data'];
    return [];
  }

  static Future<List<dynamic>> getCategories() async {
    final response = await http.get(Uri.parse('$baseUrl/categories'), headers: await _headers());
    if (response.statusCode == 200) return json.decode(response.body)['data'];
    return [];
  }

  static Future<List<dynamic>> getAccounts() async {
    final response = await http.get(Uri.parse('$baseUrl/accounts'), headers: await _headers());
    if (response.statusCode == 200) return json.decode(response.body)['data'];
    return [];
  }
}
