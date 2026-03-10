import { Component, input, output, signal } from '@angular/core';
import { MatIconModule } from '@angular/material/icon';
import { MatButtonModule } from '@angular/material/button';

export interface UploadedFile {
  file: File;
  preview: string;
  name: string;
}

@Component({
  selector: 'app-file-upload',
  imports: [MatIconModule, MatButtonModule],
  templateUrl: './file-upload.html',
  styleUrl: './file-upload.scss',
})
export class FileUpload {
  maxFiles = input<number>(5);
  accept = input<string>('image/*');

  filesSelected = output<UploadedFile[]>();

  isDragOver = signal(false);
  uploadedFiles = signal<UploadedFile[]>([]);

  onDragOver(event: DragEvent): void {
    event.preventDefault();
    this.isDragOver.set(true);
  }

  onDragLeave(): void {
    this.isDragOver.set(false);
  }

  onDrop(event: DragEvent): void {
    event.preventDefault();
    this.isDragOver.set(false);

    const files = event.dataTransfer?.files;
    if (files) {
      this.processFiles(Array.from(files));
    }
  }

  onFileSelected(event: Event): void {
    const input = event.target as HTMLInputElement;
    if (input.files) {
      this.processFiles(Array.from(input.files));
    }
  }

  removeFile(index: number): void {
    const files = this.uploadedFiles();
    files.splice(index, 1);
    this.uploadedFiles.set([...files]);
    this.filesSelected.emit(this.uploadedFiles());
  }

  private processFiles(files: File[]): void {
    const remaining = this.maxFiles() - this.uploadedFiles().length;
    const filesToProcess = files.slice(0, remaining);

    filesToProcess.forEach((file) => {
      const reader = new FileReader();
      reader.onload = (e) => {
        const current = this.uploadedFiles();
        const newFile: UploadedFile = {
          file,
          preview: e.target?.result as string,
          name: file.name,
        };
        this.uploadedFiles.set([...current, newFile]);
        this.filesSelected.emit(this.uploadedFiles());
      };
      reader.readAsDataURL(file);
    });
  }
}
