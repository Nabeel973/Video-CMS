import html2canvas from 'html2canvas';
import jsPDF from 'jspdf';
import * as XLSX from 'xlsx';

export function useExport() {
  const formatDate = (dateString) => {
    if (!dateString) return 'N/A';
    try {
      const date = new Date(dateString);
      if (isNaN(date.getTime())) return 'N/A';
      return date.toLocaleDateString('en-US', {
        year: 'numeric',
        month: 'short',
        day: 'numeric',
        hour: '2-digit',
        minute: '2-digit'
      });
    } catch (error) {
      return 'N/A';
    }
  };

  const prepareExportData = (rows, columns) => {
    const exportColumns = columns.filter(col => !col.hide && col.field !== 'actions');
    return {
      exportColumns,
      exportData: rows.map(row => {
        const newRow = {};
        exportColumns.forEach(col => {
          let value = row[col.field];
          if (col.field === 'created_at') {
            value = formatDate(value);
          } else if (col.field === 'status') {
            value = String(value).toUpperCase();
          }
          newRow[col.title] = value;
        });
        return newRow;
      })
    };
  };

  const exportToCSV = (rows, columns, filename) => {
    const { exportColumns, exportData } = prepareExportData(rows, columns);
    
    const csvContent = "data:text/csv;charset=utf-8," 
      + exportColumns.map(col => col.title).join(",") + "\n"
      + exportData.map(row => 
          exportColumns
            .map(col => `"${row[col.title]}"`)
            .join(",")
        ).join("\n");
    
    const encodedUri = encodeURI(csvContent);
    const link = document.createElement("a");
    link.setAttribute("href", encodedUri);
    link.setAttribute("download", `${filename}_${new Date().toISOString().split('T')[0]}.csv`);
    document.body.appendChild(link);
    link.click();
    document.body.removeChild(link);
  };

  const exportToExcel = (rows, columns, filename) => {
    const { exportData } = prepareExportData(rows, columns);
    
    const worksheet = XLSX.utils.json_to_sheet(exportData);
    const workbook = XLSX.utils.book_new();
    XLSX.utils.book_append_sheet(workbook, worksheet, "Sheet1");
    XLSX.writeFile(workbook, `${filename}_${new Date().toISOString().split('T')[0]}.xlsx`);
  };

  const exportToPDF = async (columns, filename) => {
    try {
      const originalTable = document.querySelector('.datatable table');
      if (!originalTable) {
        throw new Error('Table not found');
      }

      const container = document.createElement('div');
      container.style.position = 'absolute';
      container.style.left = '-9999px';
      container.style.top = '-9999px';
      document.body.appendChild(container);

      const tempTable = originalTable.cloneNode(true);
      const actionColumnIndex = columns.findIndex(col => col.field === 'actions');

      if (actionColumnIndex !== -1) {
        const rows = tempTable.querySelectorAll('tr');
        rows.forEach(row => {
          const cells = row.querySelectorAll('th, td');
          if (cells[actionColumnIndex]) {
            cells[actionColumnIndex].remove();
          }
        });
      }

      container.appendChild(tempTable);

      const doc = new jsPDF('l', 'mm', 'a4');
      const canvas = await html2canvas(tempTable, {
        scale: 2,
        logging: false,
        useCORS: true,
        allowTaint: true
      });

      const imgData = canvas.toDataURL('image/png');
      const pageWidth = doc.internal.pageSize.getWidth();
      const imgWidth = pageWidth - 20;
      const imgHeight = (canvas.height * imgWidth) / canvas.width;

      doc.addImage(imgData, 'PNG', 10, 10, imgWidth, imgHeight);
      doc.save(`${filename}_${new Date().toISOString().split('T')[0]}.pdf`);

      document.body.removeChild(container);
    } catch (error) {
      console.error('PDF Export Error:', error);
      throw error;
    }
  };

  return {
    exportToCSV,
    exportToExcel,
    exportToPDF,
    formatDate,
  };
}