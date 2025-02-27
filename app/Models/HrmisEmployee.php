<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HrmisEmployee extends Model
{
    use HasFactory;

    protected $connection = 'hrmis'; // Set the connection to HRMIS database
    protected $table = 'employee'; // Specify table name in HRMIS database

    protected $fillable = [
        'FirstName', 'MiddleName', 'LastName', 'DateOfBirth', 'PlaceOfBirth', 'Sex',
        'CivilStatus', 'Citizenship', 'TIN', 'GSISID', 'PagIbigID', 'PhilHealth',
        'SSS', 'Telephone', 'Cellphone', 'EmailAddress', 'AgencyNumber', 'isActive',
        'EmplomentStatus', 'CurrentItem', 'Department', 'Ext', 'Height', 'Weight',
        'BloodType', 'RHouseNo', 'RHouseStreet', 'RSubDivision', 'PHouseNo',
        'PHouseStreet', 'PSubDivision', 'RBarangay', 'PBarangay', 'RZip', 'PZip',
        'Prefix', 'Suffix', 'CardNo', 'AccessGroup', 'TourModule', 'MaxUnits',
        'VerifiedEmail', 'verification_code', 'profilephoto', 'Campus', 'deviceused',
        'RRegion', 'RProvince', 'PRegion', 'PProvince', 'RCityMun', 'PCityMun',
        'allergies', 'InActiveReason', 'date_of_assumption'
    ];

    public function workExperiences()
    {
        return $this->hasMany(WorkExperience::class, 'EmployeeCode', 'id');
    }

    public function designations()
    {
        return $this->hasMany(Designation::class, 'empId', 'id');
    }
}
