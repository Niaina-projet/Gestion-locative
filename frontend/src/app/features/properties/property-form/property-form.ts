import { Component, inject, signal, OnInit } from '@angular/core';
import {
  FormBuilder,
  FormGroup,
  ReactiveFormsModule,
  Validators,
} from '@angular/forms';
import { Router, ActivatedRoute } from '@angular/router';
import { MatCardModule } from '@angular/material/card';
import { MatFormFieldModule } from '@angular/material/form-field';
import { MatInputModule } from '@angular/material/input';
import { MatSelectModule } from '@angular/material/select';
import { MatButtonModule } from '@angular/material/button';
import { MatIconModule } from '@angular/material/icon';
import { MatProgressSpinnerModule } from '@angular/material/progress-spinner';
import { MatSnackBar, MatSnackBarModule } from '@angular/material/snack-bar';
import { PropertyService } from '../../../core/services/property';
import {
  FileUpload,
  UploadedFile,
} from '../../../shared/components/file-upload/file-upload';
import {
  PROPERTY_TYPES,
  PROPERTY_STATUSES,
} from '../../../models/property.model';

@Component({
  selector: 'app-property-form',
  imports: [
    ReactiveFormsModule,
    MatCardModule,
    MatFormFieldModule,
    MatInputModule,
    MatSelectModule,
    MatButtonModule,
    MatIconModule,
    MatProgressSpinnerModule,
    MatSnackBarModule,
    FileUpload,
  ],
  templateUrl: './property-form.html',
  styleUrl: './property-form.scss',
})
export class PropertyForm implements OnInit {
  private fb = inject(FormBuilder);
  private router = inject(Router);
  private route = inject(ActivatedRoute);
  private propertyService = inject(PropertyService);
  private snackBar = inject(MatSnackBar);

  isLoading = signal(false);
  isEditMode = signal(false);
  propertyId = signal<string | null>(null);
  pendingPhotos = signal<UploadedFile[]>([]);

  propertyTypes = PROPERTY_TYPES;
  propertyStatuses = PROPERTY_STATUSES;

  propertyForm: FormGroup = this.fb.group({
    title: ['', [Validators.required, Validators.maxLength(150)]],
    type: ['', Validators.required],
    address: ['', [Validators.required, Validators.maxLength(255)]],
    city: ['', [Validators.required, Validators.maxLength(100)]],
    surface: [null],
    rooms: [null],
    rentAmount: [null, [Validators.required, Validators.min(1)]],
    deposit: [null],
    status: ['available'],
    description: [''],
  });

  ngOnInit(): void {
    const id = this.route.snapshot.paramMap.get('id');
    if (id) {
      this.isEditMode.set(true);
      this.propertyId.set(id);
      this.loadProperty(id);
    }
  }

  loadProperty(id: string): void {
    this.isLoading.set(true);
    this.propertyService.getProperty(id).subscribe({
      next: (property) => {
        this.propertyForm.patchValue({
          title: property.title,
          type: property.type,
          address: property.address,
          city: property.city,
          surface: property.surface ? parseFloat(property.surface) : null,
          rooms: property.rooms,
          rentAmount: property.rentAmount,
          deposit: property.deposit,
          status: property.status,
          description: property.description,
        });
        this.isLoading.set(false);
      },
      error: () => this.isLoading.set(false),
    });
  }

  onFilesSelected(files: UploadedFile[]): void {
    this.pendingPhotos.set(files);
  }

  onSubmit(): void {
    if (this.propertyForm.invalid) {
      return;
    }

    this.isLoading.set(true);
    const formValue = this.propertyForm.value;

    const payload = {
      ...formValue,
      surface: formValue.surface ? parseFloat(formValue.surface) : null,
      rooms: formValue.rooms ? parseInt(formValue.rooms) : null,
    };

    if (this.isEditMode()) {
      this.propertyService
        .updateProperty(this.propertyId()!, payload)
        .subscribe({
          next: (property) => {
            this.uploadPendingPhotos(property.id);
          },
          error: () => {
            this.isLoading.set(false);
            this.snackBar.open('Erreur lors de la modification', 'Fermer', {
              duration: 3000,
            });
          },
        });
    } else {
      this.propertyService.createProperty(payload).subscribe({
        next: (property) => {
          this.uploadPendingPhotos(property.id);
        },
        error: () => {
          this.isLoading.set(false);
          this.snackBar.open('Erreur lors de la création', 'Fermer', {
            duration: 3000,
          });
        },
      });
    }
  }

  private uploadPendingPhotos(propertyId: string): void {
    const photos = this.pendingPhotos();

    if (photos.length === 0) {
      this.onSuccess();
      return;
    }

    let uploaded = 0;
    photos.forEach((uploadedFile) => {
      this.propertyService
        .uploadPhoto(propertyId, uploadedFile.file)
        .subscribe({
          next: () => {
            uploaded++;
            if (uploaded === photos.length) {
              this.onSuccess();
            }
          },
          error: () => {
            uploaded++;
            if (uploaded === photos.length) {
              this.onSuccess();
            }
          },
        });
    });
  }

  private onSuccess(): void {
    this.isLoading.set(false);
    const message = this.isEditMode()
      ? 'Bien modifié avec succès'
      : 'Bien créé avec succès';
    this.snackBar.open(message, 'Fermer', { duration: 3000 });
    this.router.navigate(['/properties']);
  }

  cancel(): void {
    this.router.navigate(['/properties']);
  }
}
