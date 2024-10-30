import React from 'react';

const DashboardCard = ({ title, description, icon: Icon }) => {
    return (
        <div className="bg-white rounded-lg overflow-hidden shadow-lg dashboard-card">
            <div className="p-4 relative">
                <p className="text-muted font-semibold text-sm">
                    {title}{' '}
                </p>

                <h4 className="mb-0">{description}</h4>

                <Icon
                    className="absolute right-4 bottom-4 opacity-50"
                    style={{ width: '30px', height: '30px' }}
                />
            </div>
        </div>
    );
};

export default DashboardCard;