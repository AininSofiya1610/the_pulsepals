<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    use HasFactory, Notifiable, HasRoles;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'unit_id',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
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

    // ==========================================
    // RELATIONSHIPS
    // ==========================================

    /**
     * Get the unit that the user belongs to
     */
    public function unit()
    {
        return $this->belongsTo(Unit::class);
    }

    /**
     * Get tickets assigned to this user (as technician)
     */
    public function assignedTickets()
    {
        return $this->hasMany(Ticket::class, 'assigned_to');
    }

    /**
     * Get tickets created by this user
     */
    public function createdTickets()
    {
        return $this->hasMany(Ticket::class, 'created_by');
    }

    /**
     * Get tasks assigned to this user
     */
    public function tasks()
    {
        return $this->hasMany(Task::class, 'assigned_to');
    }

    /**
     * Get leads assigned to this user
     */
    public function leads()
    {
        return $this->hasMany(Lead::class, 'assigned_to');
    }

    // ==========================================
    // HELPER METHODS
    // ==========================================

    /**
     * Check if user is an admin
     */
    public function isAdmin()
    {
        return $this->hasRole('Admin');
    }

    /**
     * Check if user is a technician
     */
    public function isTechnician()
    {
        return $this->hasAnyRole(['System Unit', 'Network & Infrastructure', 'Technical Support']);
    }

    /**
     * Get user's full role name
     */
    public function getRoleName()
    {
        return $this->roles->first()->name ?? 'No Role';
    }
}


