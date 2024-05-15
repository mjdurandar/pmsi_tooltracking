<?php

namespace App\Http\Controllers;

use App\Models\AllProducts;
use App\Models\Product;
use App\Models\ToolsAndEquipment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ExportController extends Controller
{
    public function exportToCSV()
    {
        try {
            // Execute your query to retrieve the data
            $queryResult = Product::where('user_id', Auth::id())->get();

            // Remove specific columns from each item in the collection
            $queryResult->transform(function ($item) {
                return $item->makeHidden(['id', 'user_id', 'image' ,'created_at', 'updated_at', 'is_removed', 'is_canceled', 'is_for_sale']);
            });

            // Format the query result into CSV format
            $csvContent = $this->formatQueryResultToCSV($queryResult);

            // Set response headers for file download
            $headers = array(
                'Content-Disposition' => 'attachment; filename=export.csv',
                'Content-Type' => 'text/csv',
            );

            // Return the CSV file as a response
            return response()->streamDownload(function () use ($csvContent) {
                echo $csvContent;
            }, 'products_report.csv', $headers);

        } catch (\Exception $e) {
            return response()->json(['error' => 'Error exporting CSV'], 500);
        }
    }

    private function formatQueryResultToCSV($queryResult)
    {
        // Initialize CSV content
        $csvContent = '';

        // Add CSV headers (optional)
        $headers = array_keys($queryResult[0]->toArray());
        $csvContent .= implode(',', $headers) . "\n";

        // Iterate over query result and add to CSV content
        foreach ($queryResult as $row) {
            $csvContent .= implode(',', $row->toArray()) . "\n";
        }

        return $csvContent;
    }

    public function exportToCSVAdmin()
    {
        try {
            // Execute your query to retrieve the data
            $queryResult = AllProducts::get();

            // Remove specific columns from each item in the collection
            $queryResult->transform(function ($item) {
                return $item->makeHidden(['created_at', 'updated_at']);
            });

            // Format the query result into CSV format
            $csvContent = $this->formatQueryResultToCSV($queryResult);

            // Set response headers for file download
            $headers = array(
                'Content-Disposition' => 'attachment; filename=export.csv',
                'Content-Type' => 'text/csv',
            );

            // Return the CSV file as a response
            return response()->streamDownload(function () use ($csvContent) {
                echo $csvContent;
            }, 'products_report.csv', $headers);

        } catch (\Exception $e) {
            return response()->json(['error' => 'Error exporting CSV'], 500);
        }
    }
    
    private function formatQueryResultToCSVAdmin($queryResult)
    {
        // Initialize CSV content
        $csvContent = '';
    
        // Add CSV headers (optional)
        $headers = array_keys($queryResult[0]->toArray());
        $csvContent .= implode(',', $headers) . "\n";
    
        // Iterate over query result and add to CSV content
        foreach ($queryResult as $row) {
            $csvContent .= implode(',', $row->toArray()) . "\n";
        }
    
        return $csvContent;
    }
    
    
    
}
