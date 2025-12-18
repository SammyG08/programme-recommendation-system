<?php

namespace App;


enum ElectivesEnum: string
{
    // General Arts
    case LITERATURE = 'Literature in English';
    case FRENCH = 'French';
    case AKAN = 'Akan';
    case Ga = 'Ga';
    case Ewe = 'Ewe';
    case HISTORY = 'History';
    case GEOGRAPHY = 'Geography';
    case GOVERNMENT = 'Government';
    case ECONOMICS = 'Economics';
    case CRS = 'Christian Religious Studies';
    case IRS = 'Islamic Religious Studies';


    case PHYSICS = 'Physics';
    case CHEMISTRY = 'Chemistry';
    case BIOLOGY = 'Biology';
    case EMATH = 'Elective Mathematics';


    case BUSINESS_MANAGEMENT = 'Business Management';
    case PRINCIPLES_OF_ACCOUNTING = 'Principles of Accounting';
    case COST_ACCOUNTING = 'Cost Accounting';


    case TECHNICAL_DRAWING = 'Technical Drawing';
    case BUILDING_CONSTRUCTION = 'Building Construction';
    case WOODWORK = 'Woodwork';
    case METALWORK = 'Metalwork';
    case APPLIED_ELECTRICITY = 'Applied Electricity';
    case ELECTRONICS = 'Electronics';
    case AUTO_MECHANICS = 'Auto Mechanics';


    case GENERAL_AGRICULTURE = 'General Agriculture';
    case ANIMAL_HUSBANDRY = 'Animal Husbandry';
    case CROP_HUSBANDRY = 'Crop Husbandry & Horticulture';
    case FISHERIES = 'Fisheries';
    case FORESTRY = 'Forestry';

    case GENERAL_KNOWLEDGE_IN_ART = 'General Knowledge in Art';
    case GRAPHIC_DESIGN = 'Graphic Design';
    case CERAMICS = 'Ceramics';
    case SCULPTURE = 'Sculpture';
    case LEATHERWORK = 'Leatherwork';
    case TEXTILES = 'Textiles';
    case BASKETRY = 'Basketry';
}
