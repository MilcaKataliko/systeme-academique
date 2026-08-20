<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'ecole_id',
        'photo',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    /**
     * Un utilisateur appartient à une école.
     */
    public function ecole(): BelongsTo
    {
        return $this->belongsTo(Ecole::class, 'ecole_id');
    }

    /**
     * Un utilisateur enseignant peut avoir une fiche professionnelle.
     */
    public function enseignant(): \Illuminate\Database\Eloquent\Relations\HasOne
    {
        return $this->hasOne(Enseignant::class, 'user_id');
    }

    /**
     * Vérifier si l'utilisateur a un rôle spécifique.
     */
    public function hasRole(string $role): bool
    {
        return $this->role === $role;
    }

    /**
     * Vérifier si l'utilisateur est un directeur.
     */
    public function isDirecteur(): bool
    {
        return $this->role === 'directeur';
    }

    /**
     * Vérifier si l'utilisateur est un enseignant.
     */
    public function isEnseignant(): bool
    {
        return $this->role === 'enseignant';
    }

    /**
     * Vérifier si l'utilisateur est un comptable.
     */
    public function isComptable(): bool
    {
        return $this->role === 'comptable';
    }

    /**
     * Vérifier si l'utilisateur est un élève.
     */
    public function isEleve(): bool
    {
        return $this->role === 'eleve';
    }
}
